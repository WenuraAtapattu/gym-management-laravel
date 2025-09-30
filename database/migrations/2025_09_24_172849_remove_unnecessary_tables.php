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
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop tables in the correct order to avoid foreign key constraint issues
        if (Schema::hasTable('attendances')) {
            Schema::drop('attendances');
        }
        
        // Keep memberships table
        
        // Drop other tables that don't have dependencies
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('reports');
        // Keep cart-related tables
        // Schema::dropIfExists('cart_items');
        // Schema::dropIfExists('carts');
        Schema::dropIfExists('categories');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Since this is a cleanup migration, we don't need to recreate tables in down()
    }
};
