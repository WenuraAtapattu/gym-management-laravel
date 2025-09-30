<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductSyncService;

class SyncProductsCommand extends Command
{
    protected $signature = 'products:sync 
                            {--direction=to_mongo : Sync direction: to_mongo or from_mongo}
                            {--categories : Sync only categories}';
    
    protected $description = 'Sync products between MySQL and MongoDB';

    public function handle(ProductSyncService $syncService)
    {
        $direction = $this->option('direction');
        $syncCategories = $this->option('categories');
        
        $this->info("Starting sync process...");
        
        try {
            if ($syncCategories) {
                $count = $syncService->syncCategoriesToMongoDB();
                $this->info("Synced {$count} categories to MongoDB");
                return 0;
            }
            
            $result = $syncService->fullSync($direction);
            
            if (isset($result['categories_synced'])) {
                $this->info("Synced {$result['categories_synced']} categories");
            }
            
            $this->info("Synced {$result['products_synced']} products {$result['direction']}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error during sync: " . $e->getMessage());
            return 1;
        }
    }
}