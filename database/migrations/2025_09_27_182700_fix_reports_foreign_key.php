<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // For MySQL
        if (DB::getDriverName() === 'mysql') {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Drop the foreign key constraint if it exists
            DB::statement("
                SELECT CONCAT('ALTER TABLE reports DROP FOREIGN KEY ', CONSTRAINT_NAME) 
                INTO @drop_foreign_key_sql
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'reports' 
                AND COLUMN_NAME = 'review_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
                LIMIT 1;
                
                PREPARE stmt FROM @drop_foreign_key_sql;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            ");

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    public function down()
    {
        // This is a one-way migration
    }
};