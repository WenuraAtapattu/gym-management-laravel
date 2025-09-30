<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\MongoReview;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MongoReviewController extends Controller
{
    /**
     * Get all reviews for a product.
     */
    public function index(Product $product)
    {
        $reviews = MongoReview::where('reviewable_id', $product->id)
            ->where('reviewable_type', Product::class)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate(10);

        return ReviewResource::collection($reviews);
    }

    /**
     * Store a newly created review.
     */
    public function store(Product $product, StoreReviewRequest $request): JsonResponse
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        $isAdmin = $user && $user->isAdmin();

        // Check if user/guest has already reviewed this product
        $existingReview = MongoReview::where('reviewable_id', $product->id)
            ->where('reviewable_type', Product::class);

        if ($userId) {
            $existingReview->where('user_id', $userId);
        } else if ($request->has('email')) {
            $existingReview->where('guest_email', $request->input('email'));
        } else {
            return response()->json([
                'message' => 'You must be logged in or provide an email to leave a review.',
            ], 422);
        }

        if ($existingReview->exists()) {
            return response()->json([
                'message' => 'You have already reviewed this product.',
            ], 422);
        }

        // Create the review
        $review = new MongoReview([
            'user_id' => $userId,
            'reviewable_id' => $product->id,
            'reviewable_type' => Product::class,
            'rating' => $request->input('rating'),
            'title' => $request->input('title'),
            'comment' => $request->input('comment', $request->input('content')),
            'is_approved' => $isAdmin, // Auto-approve if admin
            'guest_name' => $request->input('name'),
            'guest_email' => $request->input('email'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $review->save();

        return (new ReviewResource($review->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified review.
     */
    public function update(MongoReview $review, StoreReviewRequest $request): JsonResponse
    {
        $this->authorize('update', $review);
        
        $review->update([
            'rating' => $request->input('rating'),
            'title' => $request->input('title'),
            'comment' => $request->input('comment', $request->input('content', $review->comment)),
        ]);

        return (new ReviewResource($review->load('user')))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified review.
     */
    public function destroy(MongoReview $review): JsonResponse
    {
        $this->authorize('delete', $review);
        
        $review->delete();
        
        return response()->json([
            'message' => 'Review deleted successfully.',
        ]);
    }

    /**
     * Get review statistics.
     */
    public function stats(Product $product = null): JsonResponse
    {
        $query = MongoReview::query();
        
        if ($product) {
            $query->where('reviewable_id', $product->id)
                ->where('reviewable_type', Product::class);
        }

        $total = $query->count();
        $approved = (clone $query)->where('is_approved', true)->count();
        $pending = $total - $approved;
        $averageRating = (clone $query)->where('is_approved', true)->avg('rating');

        return response()->json([
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'average_rating' => round($averageRating, 2),
        ]);
    }
}
