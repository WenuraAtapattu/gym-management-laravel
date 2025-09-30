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
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop tables in the correct order to avoid foreign key constraint issues
        if (Schema::hasTable('attendances')) {
            Schema::drop('attendances');
        }
        
        // Keep memberships table
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     * 
     * Note: This migration is not reversible as we're dropping tables.
     * You would need to restore from a backup if you need to undo this.
     */
    public function down(): void
    {
        // This migration is not reversible
    }
};
