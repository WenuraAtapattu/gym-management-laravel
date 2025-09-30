<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReviewStatusUpdated;
use App\Notifications\NewReviewForModeration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class ReviewService
{
    /**
     * Create a new review.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The model being reviewed
     * @param User $user The user creating the review
     * @param array $data The review data
     * @param array $images Optional array of uploaded images
     * @return Review
     */
    public function createReview($model, User $user, array $data, array $images = [])
    {
        return DB::transaction(function () use ($model, $user, $data, $images) {
            // Ensure the model has a reviews relationship
            if (!method_exists($model, 'reviews')) {
                throw new \RuntimeException('The provided model does not have a reviews relationship');
            }

            $review = $model->reviews()->create([
                'user_id' => $user->id,
                'rating' => $data['rating'],
                'title' => $data['title'],
                'comment' => $data['comment'] ?? null,
                'is_approved' => !config('reviews.moderation.enabled', true),
            ]);

            // Handle image uploads if enabled
            if (config('reviews.settings.allow_images') && !empty($images)) {
                $this->processReviewImages($review, $images);
            }

            // Notify admin if moderation is enabled
            if (config('reviews.moderation.enabled', true) && config('reviews.moderation.notify_admin', true)) {
                $this->notifyAdminOfNewReview($review);
            }

            // Notify the user if moderation is needed
            if ($review->is_approved === false) {
                try {
                    $review->user->notify(new ReviewStatusUpdated(
                        $review,
                        'pending_moderation'
                    ));
                } catch (\Exception $e) {
                    // Log notification failure but don't break the flow
                    Log::error('Failed to send review notification: ' . $e->getMessage());
                }
            }

            return $review->load('user');
        });
    }

    /**
     * Update an existing review.
     */
    public function updateReview(Review $review, array $data, array $images = []): Review
    {
        return DB::transaction(function () use ($review, $data, $images) {
            $review->update([
                'rating' => $data['rating'] ?? $review->rating,
                'title' => $data['title'] ?? $review->title,
                'comment' => $data['comment'] ?? $review->comment,
            ]);

            // Handle image uploads if enabled
            if (config('reviews.settings.allow_images') && !empty($images)) {
                $this->processReviewImages($review, $images);
            }

            return $review->load('user');
        });
    }

    /**
     * Delete a review.
     */
    public function deleteReview(Review $review): bool
    {
        return DB::transaction(function () use ($review) {
            // Delete associated images if any
            if (in_array('media', get_class_methods($review))) {
                $review->media()->delete();
            }
            
            return $review->delete();
        });
    }

    /**
     * Process and store review images.
     */
    protected function processReviewImages(Review $review, array $images): void
    {
        if (!in_array('addMedia', get_class_methods($review))) {
            return;
        }

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $review->addMedia($image)
                    ->toMediaCollection('review_images');
            }
        }
    }

    /**
     * Notify admin of a new review that needs moderation.
     */
    protected function notifyAdminOfNewReview(Review $review): void
    {
        try {
            $admin = User::where('is_admin', true)->first();
            if ($admin) {
                $admin->notify(new NewReviewForModeration($review));
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admin of new review: ' . $e->getMessage());
        }
    }
}
