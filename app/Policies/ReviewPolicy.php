<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view reviews
    }
    
    /**
     * Determine whether the user can view pending reviews.
     */
    public function viewPending(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        return true; // Anyone can view individual reviews
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail(); // Only verified users can create reviews
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        // Users can update their own reviews or admins can update any
        return $user->id === $review->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can approve a review.
     */
    public function approve(User $user, Review $review): bool
    {
        return $user->isAdmin() && !$review->is_approved;
    }

    /**
     * Determine whether the user can reject a review.
     */
    public function reject(User $user, Review $review): bool
    {
        return $user->isAdmin() && !$review->is_approved;
    }

    /**
     * Determine whether the user can manage reviews.
     */
    public function manageReviews(User $user): bool
    {
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        // Users can delete their own reviews or admins can delete any
        return $user->id === $review->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Review $review): bool
    {
        return $user->isAdmin(); // Only admins can restore
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return $user->isAdmin(); // Only admins can force delete
    }

    /**
     * Determine whether the user can report a review.
     */
    public function report(User $user, Review $review): bool
    {
        // Users can't report their own reviews and must be logged in
        return $user->id !== $review->user_id && $user->hasVerifiedEmail();
    }
}
