<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\Client as MongoClient;
use MongoDB\Database as MongoDatabase;
use MongoDB\Collection as MongoCollection;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\BSONArray;
use MongoDB\BSON\ObjectId as BSONObjectId;
use MongoDB\BSON\UTCDateTime as BSONUTCDateTime;
use MongoDB\BSON\Binary as BSONBinary;
use MongoDB\BSON\Timestamp as BSONTimestamp;
use MongoDB\BSON\Regex as BSONRegex;
use MongoDB\Driver\Exception\RuntimeException as MongoDBDriverException;

/**
 * Controller for MongoDB Explorer functionality in the admin panel
 * 
 * This controller provides an interface to explore and manage MongoDB databases
 * and collections directly from the admin panel.
 */
class MongoDBExplorerController extends Controller
{
    /**
     * The MongoDB database name.
     *
     * @var string
     */
    protected $databaseName;
    
    /**
     * The MongoDB connection name.
     *
     * @var string
     */
    protected $connection = 'mongodb';
    
    /**
     * The MongoDB client instance.
     *
     * @var \MongoDB\Client
     */
    protected $mongoClient;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     * @throws \RuntimeException If the MongoDB connection fails
     */
    public function __construct()
    {
        $this->databaseName = config('database.connections.mongodb.database', 'gym_management');
        
        try {
            // Initialize the MongoDB client in the constructor to fail fast if connection fails
            $this->mongoClient = $this->getMongoClient();
        } catch (\Exception $e) {
            if (app()->environment('local')) {
                throw new \RuntimeException('Failed to initialize MongoDB client: ' . $e->getMessage());
            }
            
            // In production, log the error but don't crash the application
            logger()->error('Failed to initialize MongoDB client: ' . $e->getMessage());
        }
    }
    /**
     * Get the MongoDB client instance.
     *
     * @return \MongoDB\Client
     * @throws \RuntimeException If the connection fails
     */
    protected function getMongoClient(): \MongoDB\Client
    {
        if (isset($this->mongoClient)) {
            return $this->mongoClient;
        }

        $config = config('database.connections.mongodb');
        
        if (empty($config)) {
            throw new \RuntimeException('MongoDB configuration not found.');
        }
        
        try {
            $client = new \MongoDB\Client(
                $config['dsn'] ?? 'mongodb://127.0.0.1:27017',
                [
                    'username' => $config['username'] ?? null,
                    'password' => $config['password'] ?? null,
                    'authSource' => $config['options']['authSource'] ?? 'admin',
                    'ssl' => $config['options']['ssl'] ?? false,
                ]
            );
            
            // Test the connection
            $client->listDatabases();
            
            return $this->mongoClient = $client;
            
        } catch (\Exception $e) {
            // Catch any exception (including MongoDB\Driver\Exception\RuntimeException)
            throw new \RuntimeException('MongoDB connection failed: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * List all collections in the database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listCollections(Request $request)
    {
        try {
            $database = $this->getMongoClient()->selectDatabase($this->databaseName);
            $collections = $database->listCollections();
            
            $collectionNames = [];
            foreach ($collections as $collection) {
                $collectionNames[] = $collection->getName();
            }
            
            return response()->json([
                'status' => 'success',
                'collections' => $collectionNames
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Display the MongoDB explorer interface.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $databases = [];
        $collections = [];
        $documents = [];
        $stats = [];
        $totalDocuments = 0;
        $perPage = 25;
        $currentPage = (int) $request->query('page', 1);
        $selectedDb = $request->query('db');
        $selectedCollection = $request->query('collection');
        $searchTerm = $request->query('search');
        
        try {
            // List all databases
            $databaseList = $this->mongoClient->listDatabases();
            $databases = iterator_to_array($databaseList);
            
            // If a database is selected, list its collections
            if ($selectedDb) {
                $db = $this->mongoClient->selectDatabase($selectedDb);
                
                // Get database stats
                $statsResult = $db->command(['dbStats' => 1]);
                $stats = (array) $statsResult->toArray()[0] ?? [];
                
                // List collections
                $collections = iterator_to_array($db->listCollections());
                
                // If a collection is selected, get its documents with pagination
                if ($selectedCollection) {
                    $collection = $db->selectCollection($selectedCollection);
                    
                    // Build query
                    $query = [];
                    if ($searchTerm) {
                        $query['$or'] = [
                            ['_id' => new \MongoDB\BSON\Regex($searchTerm, 'i')],
                            // Add more fields to search as needed
                        ];
                    }
                    
                    // Get total count for pagination
                    $totalDocuments = $collection->countDocuments($query);
                    
                    // Calculate pagination
                    $totalPages = ceil($totalDocuments / $perPage);
                    $skip = ($currentPage - 1) * $perPage;
                    
                    // Get documents with pagination
                    $cursor = $collection->find(
                        $query,
                        [
                            'limit' => $perPage,
                            'skip' => $skip,
                            'sort' => ['_id' => -1]
                        ]
                    );
                    
                    // Convert cursor to array
                    $documents = [];
                    foreach ($cursor as $document) {
                        $documents[] = $document;
                    }
                }
            }
            
            return view('admin.mongodb-explorer.index', [
                'databases' => $databases,
                'collections' => $collections,
                'documents' => $documents,
                'selectedDb' => $selectedDb,
                'selectedCollection' => $selectedCollection,
                'stats' => $stats,
                'searchTerm' => $searchTerm,
                'currentPage' => $currentPage,
                'perPage' => $perPage,
                'totalDocuments' => $totalDocuments,
                'totalPages' => $totalPages ?? 1,
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Execute a MongoDB query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function query(Request $request)
    {
        $request->validate([
            'database' => 'required|string',
            'collection' => 'required|string',
            'query' => 'required|json',
            'projection' => 'nullable|json',
            'sort' => 'nullable|json',
            'limit' => 'nullable|integer|min:1|max:1000',
            'skip' => 'nullable|integer|min:0',
        ]);
        
        try {
            $db = $this->mongoClient->selectDatabase($request->input('database'));
            $collection = $db->selectCollection($request->input('collection'));
            
            // Parse query
            $query = $this->parseJsonInput($request->input('query'));
            
            // Build options
            $options = [
                'limit' => min(100, (int) ($request->input('limit', 100))),
                'sort' => $this->parseJsonInput($request->input('sort', '{}')),
            ];
            
            if ($request->has('skip')) {
                $options['skip'] = (int) $request->input('skip');
            }
            
            if ($request->has('projection')) {
                $options['projection'] = $this->parseJsonInput($request->input('projection'));
            }
            
            // Execute query
            $cursor = $collection->find($query, $options);
            $documents = iterator_to_array($cursor);
            
            // Get count for the query (without limit/skip for accurate count)
            $count = $collection->countDocuments($query);
            
            return response()->json([
                'status' => 'success',
                'count' => $count,
                'returned' => count($documents),
                'data' => $documents,
                'query' => $query,
                'options' => $options,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
                'code' => method_exists($e, 'getCode') ? $e->getCode() : 500,
            ], 500);
        }
    }
    
    /**
     * Create a new document in the specified collection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'database' => 'required|string',
            'collection' => 'required|string',
            'document' => 'required|json',
        ]);
        
        try {
            $db = $this->mongoClient->selectDatabase($request->input('database'));
            $collection = $db->selectCollection($request->input('collection'));
            
            // Parse and validate document
            $document = $this->parseJsonInput($request->input('document'));
            
            // Insert the document
            $result = $collection->insertOne($document);
            
            // Get the inserted document
            $insertedDocument = $collection->findOne(['_id' => $result->getInsertedId()]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Document created successfully',
                'id' => (string) $result->getInsertedId(),
                'document' => $insertedDocument,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
                'code' => method_exists($e, 'getCode') ? $e->getCode() : 500,
            ], 500);
        }
    }
    
    /**
     * Update a document in the specified collection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'database' => 'required|string',
            'collection' => 'required|string',
            'document' => 'required|json',
        ]);
        
        try {
            $db = $this->mongoClient->selectDatabase($request->input('database'));
            $collection = $db->selectCollection($request->input('collection'));
            
            // Parse and validate document
            $updates = $this->parseJsonInput($request->input('document'));
            
            // Remove _id from updates to prevent modification
            unset($updates['_id']);
            
            // Update the document
            $result = $collection->updateOne(
                ['_id' => $this->createObjectId($id)],
                ['$set' => $updates]
            );
            
            if ($result->getModifiedCount() === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No document was updated. The document may not exist or the data may be the same.',
                ], 404);
            }
            
            // Get the updated document
            $updatedDocument = $collection->findOne(['_id' => $this->createObjectId($id)]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Document updated successfully',
                'modifiedCount' => $result->getModifiedCount(),
                'document' => $updatedDocument,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
                'code' => method_exists($e, 'getCode') ? $e->getCode() : 500,
            ], 500);
        }
    }
    
    /**
     * Delete a document from the specified collection.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $request = request();
        $request->validate([
            'database' => 'required|string',
            'collection' => 'required|string',
        ]);
        
        try {
            $db = $this->mongoClient->selectDatabase($request->input('database'));
            $collection = $db->selectCollection($request->input('collection'));
            
            // Delete the document
            $result = $collection->deleteOne(['_id' => $this->createObjectId($id)]);
            
            if ($result->getDeletedCount() === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No document was deleted. The document may not exist.',
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Document deleted successfully',
                'deletedCount' => $result->getDeletedCount(),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
                'code' => method_exists($e, 'getCode') ? $e->getCode() : 500,
            ], 500);
        }
    }
    
    /**
     * Get a single document by ID.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function document($id)
    {
        $request = request();
        $database = $request->query('database');
        $collectionName = $request->query('collection');
        
        if (!$database || !$collectionName) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database and collection parameters are required',
                'code' => 400,
            ], 400);
        }
        
        try {
            $db = $this->mongoClient->selectDatabase($database);
            $collection = $db->selectCollection($collectionName);
            
            $document = $collection->findOne(['_id' => $this->createObjectId($id)]);
            
            if (!$document) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Document not found',
                    'code' => 404,
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $document,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
                'code' => method_exists($e, 'getCode') ? $e->getCode() : 500,
            ], 500);
        }
    }
    
    /**
     * Parse JSON input and handle special MongoDB types.
     *
     * @param  string  $json
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function parseJsonInput($json)
    {
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        
        return $this->convertToMongoTypes($data);
    }
    
    /**
     * Recursively convert array values to MongoDB types.
     *
     * @param  mixed  $data
     * @return mixed
     */
    /**
     * Recursively convert array values to MongoDB types.
     *
     * @param  mixed  $data
     * @return mixed
     */
    /**
     * Recursively convert array values to MongoDB types.
     *
     * @param  mixed  $data
     * @return mixed
     */
    /**
     * Recursively convert array values to MongoDB types.
     *
     * @param  mixed  $data
     * @return mixed
     */
    /**
     * Convert data to MongoDB types.
     *
     * @param mixed $data
     * @return mixed
     */
    /**
     * Convert data to MongoDB types.
     *
     * @param mixed $data
     * @return mixed
     */
    /**
     * Convert data to MongoDB types
     * 
     * @param mixed $data
     * @return \MongoDB\BSON\ObjectId|\MongoDB\BSON\UTCDateTime|\MongoDB\BSON\Binary|\MongoDB\BSON\Timestamp|\MongoDB\BSON\Regex|array|object|mixed
     */
    protected function convertToMongoTypes($data)
    {
        if (is_array($data)) {
            // Handle MongoDB operators (start with $)
            if (count($data) === 1) {
                $key = array_key_first($data);
                $value = $data[$key];
                
                if (is_string($value) && preg_match('/^[0-9a-f]{24}$/i', $value)) {
                    // Try to convert string to ObjectId if it looks like one
                    try {
                        return new \MongoDB\BSON\ObjectId($value);
                    } catch (\Exception $e) {
                        // If conversion fails, keep the original value
                        return $value;
                    }
                } elseif (is_string($value) && ($timestamp = strtotime($value)) !== false) {
                    // Try to convert string to UTCDateTime if it's a valid date
                    return new \MongoDB\BSON\UTCDateTime($timestamp * 1000);
                } elseif (is_string($value) && preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $value)) {
                    // Try to decode base64 string to Binary
                    $decoded = base64_decode($value, true);
                    if ($decoded !== false) {
                        return new \MongoDB\BSON\Binary($decoded, \MongoDB\BSON\Binary::TYPE_GENERIC);
                    }
                } elseif (is_array($value) && isset($value['$timestamp'])) {
                    // Handle MongoDB Timestamp
                    return new \MongoDB\BSON\Timestamp($value['$timestamp']['t'], $value['$timestamp']['i']);
                } elseif (is_array($value) && isset($value['$regex'])) {
                    // Handle MongoDB Regex
                    return new \MongoDB\BSON\Regex(
                        $value['$regex'],
                        $value['$options'] ?? ''
                    );
                }
            }
            
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->convertToMongoTypes($value);
            }
            return (object) $result;
        }
        
        return $data;
    }
    
    /**
     * Create a MongoDB ObjectId from a string
     *
     * @param string $id
     * @return \MongoDB\BSON\ObjectId
     * @throws \InvalidArgumentException If the ID is not a valid ObjectId
     */
    protected function createObjectId($id)
    {
        try {
            return new \MongoDB\BSON\ObjectId($id);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid ObjectId: ' . $id);
        }
    }
    
    /**
     * Get the MongoDB database instance.
     *
     * @param  string|null  $database
     * @return \MongoDB\Database
     */
    protected function getDatabase($database = null)
    {
        return $this->getMongoClient()->selectDatabase($database ?: $this->databaseName);
    }
    
    /**
     * Get the MongoDB collection instance.
     *
     * @param  string  $collection
     * @param  string|null  $database
     * @return \MongoDB\Collection
     */
    protected function getCollection($collection, $database = null)
    {
        return $this->getDatabase($database)->selectCollection($collection);
    }
    
    
    /**
     * Convert MongoDB document to array.
     *
     * @param  mixed  $document
     * @return array|mixed
     */
    /**
     * Convert a MongoDB document to an array.
     *
     * @param  mixed  $document
     * @return mixed
     */
    /**
     * Convert a MongoDB document to an array.
     *
     * @param  mixed  $document
     * @return mixed
     */
    /**
     * Convert a MongoDB document to an array.
     *
     * @param  mixed  $document
     * @return mixed
     */
    /**
     * Convert a MongoDB document to an array.
     *
     * @param mixed $document
     * @return mixed
     */
    /**
     * Convert a MongoDB document to an array.
     *
     * @param mixed $document
     * @return mixed
     */
    /**
     * Convert a MongoDB document to an array
     * 
     * @param BSONObjectId|BSONUTCDateTime|BSONBinary|BSONTimestamp|BSONRegex|BSONDocument|BSONArray|array|\ArrayObject|mixed $document
     * @return array|string|\DateTime|mixed
     */
    /**
     * Convert a MongoDB document to an array
     *
     * @param mixed $document
     * @return array|string|\DateTime|mixed
     */
    protected function documentToArray($document)
    {
        if ($document instanceof \MongoDB\BSON\ObjectId) {
            return (string) $document;
        } elseif ($document instanceof \MongoDB\BSON\UTCDateTime) {
            return $document->toDateTime();
        } elseif ($document instanceof \MongoDB\BSON\Binary) {
            return base64_encode($document->getData());
        } elseif ($document instanceof \MongoDB\BSON\Timestamp) {
            return [
                'timestamp' => $document->getTimestamp(),
                'increment' => $document->getIncrement(),
            ];
        } elseif ($document instanceof \MongoDB\BSON\Regex) {
            return [
                'pattern' => $document->getPattern(),
                'flags' => $document->getFlags(),
            ];
        } elseif ($document instanceof BSONDocument || $document instanceof BSONArray) {
            return $this->documentToArray($document->getArrayCopy());
        } elseif (is_array($document) || $document instanceof \ArrayObject) {
            $result = [];
            foreach ($document as $key => $value) {
                $result[$key] = $this->documentToArray($value);
            }
            return $result;
        } elseif (is_object($document) && method_exists($document, 'jsonSerialize')) {
            return $this->documentToArray($document->jsonSerialize());
        }
        
        return $document;
    }
}
