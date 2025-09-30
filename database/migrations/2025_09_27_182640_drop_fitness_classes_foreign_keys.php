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
        
        // Drop foreign key constraints if they exist
        try {
            Schema::table('fitness_classes', function (Blueprint $table) {
                // Get the actual foreign key name
                $foreignKeys = DB::select(
                    "SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_NAME = 'fitness_classes' 
                    AND COLUMN_NAME = 'instructor_id' 
                    AND REFERENCED_TABLE_NAME = 'instructors'"
                );
                
                if (!empty($foreignKeys)) {
                    $foreignKeyName = $foreignKeys[0]->CONSTRAINT_NAME;
                    $table->dropForeign($foreignKeyName);
                }
            });
        } catch (\Exception $e) {
            // Ignore errors if the foreign key doesn't exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the foreign key constraint if needed
        Schema::table('fitness_classes', function (Blueprint $table) {
            $table->foreign('instructor_id')
                  ->references('id')
                  ->on('instructors')
                  ->onDelete('cascade');
        });
    }
};
