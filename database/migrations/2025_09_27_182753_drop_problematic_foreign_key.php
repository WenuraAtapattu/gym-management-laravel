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
        if (!Schema::hasTable('fitness_classes')) {
            return;
        }
        
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we don't want to recreate the problematic constraint
    }
};
