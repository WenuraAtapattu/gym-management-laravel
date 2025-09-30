<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Display a listing of pending reviews.
     */
    public function pendingReviews(): JsonResponse
    {
        $reviews = Review::with(['user', 'product'])
            ->where('is_approved', false)
            ->latest()
            ->paginate(10);

        return ReviewResource::collection($reviews)->response();
    }

    /**
     * Approve a review.
     */
    public function approve(Review $review): JsonResponse
    {
        $review->update(['is_approved' => true]);

        return response()->json([
            'message' => 'Review approved successfully.',
            'data' => new ReviewResource($review->load('user', 'product'))
        ]);
    }

    /**
     * Reject a review.
     */
    public function reject(Review $review): JsonResponse
    {
        $review->delete();

        return response()->json([
            'message' => 'Review rejected and deleted successfully.'
        ]);
    }

    /**
     * Get review statistics.
     */
    public function stats(): JsonResponse
    {
        $totalReviews = Review::count();
        $pendingReviews = Review::where('is_approved', false)->count();
        $approvedReviews = Review::where('is_approved', true)->count();

        return response()->json([
            'total_reviews' => $totalReviews,
            'pending_reviews' => $pendingReviews,
            'approved_reviews' => $approvedReviews,
        ]);
    }
}
