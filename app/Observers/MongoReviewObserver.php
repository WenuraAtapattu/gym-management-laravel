<?php

namespace App\Observers;

use App\Models\MongoProduct;
use App\Models\MongoReview;

class MongoReviewObserver
{
    /**
     * Handle the MongoReview "created" event.
     */
    public function created(MongoReview $review): void
    {
        $productId = $review->getAttribute('product_id');
        if ($productId) {
            $this->updateProductRating($productId);
        }
    }

    /**
     * Handle the MongoReview "updated" event.
     */
    public function updated(MongoReview $review): void
    {
        $productId = $review->getAttribute('product_id');
        if ($productId) {
            $this->updateProductRating($productId);
        }
    }

    /**
     * Handle the MongoReview "deleted" event.
     */
    public function deleted(MongoReview $review): void
    {
        $productId = $review->getAttribute('product_id');
        if ($productId) {
            $this->updateProductRating($productId);
        }
    }

    /**
     * Update the product's rating based on its reviews.
     */
    private function updateProductRating(string $productId): void
    {
        $product = MongoProduct::find($productId);
        if (!$product) {
            return;
        }

        // Use the relationship if it exists, otherwise query directly
        $reviews = method_exists($product, 'reviews') 
            ? $product->reviews() 
            : MongoReview::where('product_id', $productId);
            
        $averageRating = $reviews->avg('rating');
        $reviewCount = $reviews->count();

        $product->update([
            'average_rating' => $averageRating ?? 0,
            'review_count' => $reviewCount,
        ]);
    }
}
