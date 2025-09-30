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
        // Skip if the tables don't exist
        if (!Schema::hasTable('fitness_classes') || !Schema::hasTable('instructors')) {
            return;
        }
        
        // Drop foreign key constraints first if they exist
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
        
        // We're not dropping the instructors table anymore
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the tables and foreign keys if needed
        // Note: This is a simplified version - you might need to adjust based on your schema
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('specialty')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
        
        Schema::table('fitness_classes', function (Blueprint $table) {
            $table->foreign('instructor_id')
                  ->references('id')
                  ->on('instructors')
                  ->onDelete('cascade');
        });
    }
};
