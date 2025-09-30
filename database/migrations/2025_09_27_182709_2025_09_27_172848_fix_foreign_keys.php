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
        // Drop foreign key constraints
        Schema::table('fitness_classes', function (Blueprint $table) {
            // Disable foreign key checks
            if (DB::getDriverName() === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            // Drop foreign key constraint if it exists
            $sql = 'SELECT * FROM information_schema.TABLE_CONSTRAINTS 
                WHERE CONSTRAINT_TYPE = "FOREIGN KEY" 
                AND TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = "fitness_classes" 
                AND CONSTRAINT_NAME = "fitness_classes_instructor_id_foreign"';
                
            $foreignKeys = DB::select($sql);

            if (count($foreignKeys) > 0) {
                $table->dropForeign('fitness_classes_instructor_id_foreign');
            }

            if (DB::getDriverName() === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we don't want to recreate the constraint
        // that was causing issues
    }
};
