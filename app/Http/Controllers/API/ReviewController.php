<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ReviewController extends Controller
{
    /**
     * Get all reviews for a product.
     */
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user')
            ->where('is_approved', true)
            ->latest()
            ->paginate(10);

        return ReviewResource::collection($reviews)->response();
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
        $existingReviewQuery = $product->reviews();
        
        if ($userId) {
            $existingReviewQuery->where('user_id', $userId);
        } else if ($request->has('email')) {
            $existingReviewQuery->where('guest_email', $request->input('email'));
        } else {
            return response()->json([
                'message' => 'You must be logged in or provide an email to leave a review.',
            ], 422);
        }

        if ($existingReviewQuery->exists()) {
            return response()->json([
                'message' => 'You have already reviewed this product.',
            ], 422);
        }

        // Get validated data
        $validated = $request->validated();
        
        // Create the review
        $review = new Review([
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'] ?? $validated['content'] ?? null,
            'user_id' => $userId,
            'is_approved' => $isAdmin, // Auto-approve if admin
            'guest_name' => $request->input('name'),
            'guest_email' => $request->input('email'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        $product->reviews()->save($review);

        // Handle review images if any
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $review->addMedia($image)->toMediaCollection('review_images');
            }
        }

        return (new ReviewResource($review->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified review.
     */
    public function update(Review $review, StoreReviewRequest $request): JsonResponse
    {
        $this->authorize('update', $review);
        
        $validated = $request->validated();
        $review->update([
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'] ?? $validated['content'] ?? $review->comment,
        ]);

        // Handle review images if any
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $review->addMedia($image)->toMediaCollection('review_images');
            }
        }

        return (new ReviewResource($review->load('user')))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review): JsonResponse
    {
        $this->authorize('delete', $review);
        
        $review->delete();
        
        return response()->json([
            'message' => 'Review deleted successfully.',
        ]);
    }

    /**
     * Report a review.
     */
    public function report(Review $review, Request $request): JsonResponse
    {
        $this->authorize('report', $review);

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // Create a report record (you'll need to create this model and migration)
        $review->reports()->create([
            'user_id' => Auth::id(),
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'Thank you for your report. Our team will review it shortly.',
        ]);
    }
}
