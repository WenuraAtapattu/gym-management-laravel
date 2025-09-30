<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Product $product)
    {
        // Validate the request
        $validated = $request->validate(Review::rules());
        
        // Check if the user has already reviewed this product
        if ($product->reviews()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Create the review with polymorphic relationship
        $review = new Review($validated);
        $review->user_id = Auth::id();
        $review->is_approved = true; // Or set based on your moderation settings
        
        // Associate the review with the product using the polymorphic relationship
        $product->reviews()->save($review);

        // Recalculate product rating
        $this->updateProductRating($product);

        return back()->with('success', 'Thank you for your review!');
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, Review $review)
    {
        // Authorize the update
        if (Gate::denies('update', $review)) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the request
        $validated = $request->validate(Review::rules());
        
        // Update the review
        $review->update($validated);

        // Recalculate product rating
        $this->updateProductRating($review->product);

        return back()->with('success', 'Review updated successfully.');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        // Authorize the delete
        if (Gate::denies('delete', $review)) {
            abort(403, 'Unauthorized action.');
        }

        $product = $review->product;
        $review->delete();

        // Recalculate product rating
        $this->updateProductRating($product);

        return back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Recalculate and update the product's average rating.
     */
    protected function updateProductRating(Product $product): void
    {
        $product->update([
            'rating' => $product->reviews()
                ->where('is_approved', true)
                ->avg('rating')
        ]);
    }

    /**
     * Get all reviews for a product.
     */
    public function getProductReviews(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user')
            ->where('is_approved', true)
            ->latest()
            ->paginate(5);

        return response()->json($reviews);
    }

    /**
     * Get the review statistics for a product.
     */
    public function getReviewStats(Product $product)
    {
        $stats = [
            'average_rating' => $product->reviews()->avg('rating') ?? 0,
            'total_reviews' => $product->reviews()->count(),
            'rating_counts' => $product->reviews()
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->orderBy('rating', 'desc')
                ->pluck('count', 'rating')
                ->toArray(),
        ];

        return response()->json($stats);
    }
}
