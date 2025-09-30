<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip if the table doesn't exist
        if (!Schema::hasTable('fitness_classes')) {
            return;
        }
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Get the foreign key name
            $foreignKeys = DB::select(
                "SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'fitness_classes' 
                AND COLUMN_NAME = 'instructor_id' 
                AND REFERENCED_TABLE_NAME = 'instructors'"
            );
            
            // Drop the foreign key if it exists
            if (!empty($foreignKeys)) {
                $foreignKeyName = $foreignKeys[0]->CONSTRAINT_NAME;
                DB::statement("ALTER TABLE fitness_classes DROP FOREIGN KEY `$foreignKeyName`");
            }
        } catch (\Exception $e) {
            // Ignore any errors
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we don't want to recreate the problematic constraint
    }
};
