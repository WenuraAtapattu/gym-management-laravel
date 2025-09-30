<?php

namespace App\Services;

use App\Models\Product as MySQLProduct;
use App\Models\MongoProduct;
use App\Models\Category as MySQLCategory;
use App\Models\MongoCategory;
use Illuminate\Support\Facades\Log;

class ProductSyncService
{
    /**
     * Sync products from MySQL to MongoDB
     */
    public function syncToMongoDB()
    {
        try {
            $mysqlProducts = MySQLProduct::with('category')->get();
            $synced = 0;
            
            foreach ($mysqlProducts as $product) {
                $productData = $product->toArray();
                $productData['category_id'] = $product->category_id ? (string) $product->category_id : null;
                
                // Convert timestamps if needed
                if (isset($productData['created_at'])) {
                    $productData['created_at'] = $productData['created_at'] instanceof \DateTime 
                        ? $productData['created_at']->format('Y-m-d H:i:s')
                        : $productData['created_at'];
                }
                if (isset($productData['updated_at'])) {
                    $productData['updated_at'] = $productData['updated_at'] instanceof \DateTime 
                        ? $productData['updated_at']->format('Y-m-d H:i:s')
                        : $productData['updated_at'];
                }
                
                MongoProduct::updateOrCreate(
                    ['_id' => $product->id],
                    $productData
                );
                $synced++;
            }
            
            return $synced;
        } catch (\Exception $e) {
            Log::error('Error syncing to MongoDB: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Sync products from MongoDB to MySQL
     */
    public function syncFromMongoDB()
    {
        try {
            $mongoProducts = MongoProduct::all();
            $synced = 0;
            
            foreach ($mongoProducts as $product) {
                $productData = $product->toArray();
                unset($productData['_id']); // Remove MongoDB _id
                
                // Ensure category_id is properly cast
                if (isset($productData['category_id'])) {
                    $productData['category_id'] = (int) $productData['category_id'];
                }
                
                MySQLProduct::updateOrCreate(
                    ['id' => $product->id],
                    $productData
                );
                $synced++;
            }
            
            return $synced;
        } catch (\Exception $e) {
            Log::error('Error syncing from MongoDB: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Sync categories from MySQL to MongoDB
     */
    public function syncCategoriesToMongoDB()
    {
        try {
            $mysqlCategories = MySQLCategory::all();
            $synced = 0;
            
            foreach ($mysqlCategories as $category) {
                $categoryData = $category->toArray();
                
                // Handle parent_id conversion
                if (isset($categoryData['parent_id'])) {
                    $categoryData['parent_id'] = $categoryData['parent_id'] ? (string) $categoryData['parent_id'] : null;
                }
                
                MongoCategory::updateOrCreate(
                    ['_id' => $category->id],
                    $categoryData
                );
                $synced++;
            }
            
            return $synced;
        } catch (\Exception $e) {
            Log::error('Error syncing categories to MongoDB: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Full sync including categories and products
     */
    public function fullSync($direction = 'to_mongo')
    {
        if ($direction === 'to_mongo') {
            $categoriesSynced = $this->syncCategoriesToMongoDB();
            $productsSynced = $this->syncToMongoDB();
            
            return [
                'categories_synced' => $categoriesSynced,
                'products_synced' => $productsSynced,
                'direction' => 'to_mongo'
            ];
        } else {
            $productsSynced = $this->syncFromMongoDB();
            
            return [
                'products_synced' => $productsSynced,
                'direction' => 'from_mongo'
            ];
        }
    }
}