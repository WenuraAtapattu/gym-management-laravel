<?php

namespace App\Policies;

use App\Models\MongoReview;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MongoReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MongoReview $review): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Guests can also create reviews
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MongoReview $review): bool
    {
        // Only the review owner or an admin can update
        return $user->getKey() === $review->getAttribute('user_id') || 
               $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MongoReview $review): bool
    {
        // Only the review owner or an admin can delete
        return $user->getKey() === $review->getAttribute('user_id') || 
               $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MongoReview $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MongoReview $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can report the review.
     */
    public function report(User $user, MongoReview $review): bool
    {
        // Users can report reviews that aren't their own
        return $user->getKey() !== $review->getAttribute('user_id');
    }
}
