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
        Schema::table('reviews', function (Blueprint $table) {
            // Rename product_id to reviewable_id if it exists
            if (Schema::hasColumn('reviews', 'product_id')) {
                $table->renameColumn('product_id', 'reviewable_id');
            }
            
            // Add reviewable_type if it doesn't exist
            if (!Schema::hasColumn('reviews', 'reviewable_type')) {
                $table->string('reviewable_type')->default('App\\Models\\Product')->after('reviewable_id');
            }
            
            // Add guest information if they don't exist
            if (!Schema::hasColumn('reviews', 'guest_name')) {
                $table->string('guest_name')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('reviews', 'guest_email')) {
                $table->string('guest_email')->nullable()->after('guest_name');
            }
            
            // Add moderation fields if they don't exist
            if (!Schema::hasColumn('reviews', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('comment');
            }
            
            if (!Schema::hasColumn('reviews', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('is_approved');
            }
            
            if (!Schema::hasColumn('reviews', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            
            // Add indexes if they don't exist
            if (!Schema::hasIndex('reviews', ['reviewable_id', 'reviewable_type'])) {
                $table->index(['reviewable_id', 'reviewable_type']);
            }
            
            if (!Schema::hasIndex('reviews', ['is_approved'])) {
                $table->index('is_approved');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Revert column renames
            $table->renameColumn('reviewable_id', 'product_id');
            
            // Drop added columns
            $table->dropColumn([
                'reviewable_type',
                'guest_name',
                'guest_email',
                'is_approved',
                'ip_address',
                'user_agent'
            ]);
            
            // Drop indexes
            $table->dropIndex(['reviewable_id', 'reviewable_type']);
            $table->dropIndex(['is_approved']);
        });
    }
};
