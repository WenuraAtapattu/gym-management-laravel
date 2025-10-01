<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateReviewsCollection extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MongoDB doesn't require explicit collection creation
        // We'll just create an index for better query performance
        Schema::connection('mongodb')->create('reviews', function (Blueprint $collection) {
            $collection->index('user_id');
            $collection->index('reviewable_id');
            $collection->index('reviewable_type');
            $collection->index('is_approved');
            $collection->index('rating');
            $collection->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('reviews');
    }
};
