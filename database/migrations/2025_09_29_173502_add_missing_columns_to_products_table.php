<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('name');
            $table->decimal('compare_at_price', 10, 2)->nullable()->after('price');
            $table->decimal('cost_per_item', 10, 2)->nullable()->after('compare_at_price');
            $table->string('barcode')->nullable()->after('cost_per_item');
            $table->boolean('has_stock')->default(true)->after('stock_quantity');
            $table->boolean('is_bestseller')->default(false)->after('is_featured');
            $table->boolean('is_new_arrival')->default(false)->after('is_bestseller');
            $table->string('seo_title')->nullable()->after('is_active');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_keywords')->nullable()->after('seo_description');
            
            // Make category_id nullable
            $table->foreignId('category_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'brand',
                'compare_at_price',
                'cost_per_item',
                'barcode',
                'has_stock',
                'is_bestseller',
                'is_new_arrival',
                'seo_title',
                'seo_description',
                'seo_keywords'
            ];
            
            if (Schema::hasColumns('products', $columns)) {
                $table->dropColumn($columns);
            }
            
            // Revert category_id to not nullable
            $table->foreignId('category_id')->nullable(false)->change();
        });
    }
};
