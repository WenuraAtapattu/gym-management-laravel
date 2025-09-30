<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReviewController extends Controller
{
    /**
     * Display a listing of the pending reviews.
     */
    /**
     * Display the admin reviews index page.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/Reviews/Index', [
            'pendingReviews' => $this->getPendingReviews(),
            'approvedReviews' => $this->getApprovedReviews(),
            'stats' => $this->getReviewStats(),
        ]);
    }

    /**
     * Get paginated pending reviews.
     */
    public function pending(Request $request)
    {
        $reviews = $this->getPendingReviews();
        return response()->json($reviews);
    }

    /**
     * Get paginated approved reviews.
     */
    public function approved(Request $request)
    {
        $reviews = $this->getApprovedReviews();
        return response()->json($reviews);
    }

    /**
     * Get pending reviews query.
     */
    protected function getPendingReviews()
    {
        return Review::with(['user', 'reviewable'])
            ->where('is_approved', false)
            ->latest()
            ->paginate(10);
    }

    /**
     * Get approved reviews query.
     */
    protected function getApprovedReviews()
    {
        return Review::with(['user', 'reviewable'])
            ->where('is_approved', true)
            ->latest()
            ->paginate(10);
    }

    /**
     * Get review statistics.
     */
    protected function getReviewStats(): array
    {
        return [
            'total_reviews' => Review::count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'approved_reviews' => Review::where('is_approved', true)->count(),
        ];
    }

    /**
     * Approve the specified review.
     */
    public function approve(Review $review)
    {
        $this->authorize('approve', $review);
        
        $review->update(['is_approved' => true]);
        
        return response()->json([
            'message' => 'Review approved successfully.',
            'review' => $review->load('user', 'reviewable'),
        ]);
    }

    /**
     * Reject the specified review.
     */
    public function reject(Review $review)
    {
        $this->authorize('reject', $review);
        
        $review->delete();
        
        return response()->json([
            'message' => 'Review rejected successfully.',
        ]);
    }

    /**
     * Get review statistics.
     */
    public function stats()
    {
        return response()->json([
            'total_reviews' => Review::count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'approved_reviews' => Review::where('is_approved', true)->count(),
        ]);
    }
}
