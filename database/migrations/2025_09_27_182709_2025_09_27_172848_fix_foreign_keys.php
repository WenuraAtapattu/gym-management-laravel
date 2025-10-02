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
        // Skip this migration for SQLite as it doesn't support the same foreign key operations
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('fitness_classes', function (Blueprint $table) {
                // For MySQL/MariaDB
                if (DB::getDriverName() === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    $table->dropForeignIfExists('fitness_classes_instructor_id_foreign');
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                } 
                // For PostgreSQL
                else if (DB::getDriverName() === 'pgsql') {
                    $table->dropForeignIfExists('fitness_classes_instructor_id_foreign');
                }
            });
        }
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
