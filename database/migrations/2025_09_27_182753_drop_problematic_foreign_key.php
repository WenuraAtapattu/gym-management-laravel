<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip if the table doesn't exist
        if (!Schema::hasTable('fitness_classes') || DB::getDriverName() === 'sqlite') {
            return;
        }
        
        // For MySQL/MariaDB
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Drop the foreign key if it exists using a try-catch block
            try {
                Schema::table('fitness_classes', function ($table) {
                    $table->dropForeign(['instructor_id']);
                });
            } catch (\Exception $e) {
                // Ignore errors if the foreign key doesn't exist
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        // For PostgreSQL
        else if (DB::getDriverName() === 'pgsql') {
            try {
                Schema::table('fitness_classes', function ($table) {
                    $table->dropForeign(['instructor_id']);
                });
            } catch (\Exception $e) {
                // Ignore errors if the foreign key doesn't exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we don't want to recreate the problematic constraint
    }
};
