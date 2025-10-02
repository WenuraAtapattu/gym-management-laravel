<?php

use App\Http\Controllers\Admin\MongoDBExplorerController;
use Illuminate\Http\Request;

Route::get('/test-mongodb-explorer', function () {
    try {
        $controller = new MongoDBExplorerController();
        $request = new Request();
        
        // Test listing collections
        $collections = $controller->listCollections($request);
        
        return response()->json([
            'status' => 'success',
            'collections' => $collections->getData()->collections ?? []
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});
