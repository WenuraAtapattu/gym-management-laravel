<?php

namespace App\Repositories;

use App\Models\Review;
use App\Models\MongoReview;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model as MongoModel;

class ReviewRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = config('database.default') === 'mongodb' 
            ? new MongoReview() 
            : new Review();
    }

    /**
     * Get all reviews for a reviewable item.
     */
    public function getForReviewable($reviewableId, $reviewableType, $approvedOnly = true)
    {
        $query = $this->model->where('reviewable_id', $reviewableId)
            ->where('reviewable_type', $reviewableType)
            ->with('user');

        if ($approvedOnly) {
            $query->where('is_approved', true);
        }

        return $query->latest()->get();
    }

    /**
     * Create a new review.
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a review.
     */
    public function update($id, array $data)
    {
        $review = $this->model->findOrFail($id);
        $review->update($data);
        return $review;
    }

    /**
     * Delete a review.
     */
    public function delete($id)
    {
        $review = $this->model->findOrFail($id);
        return $review->delete();
    }

    /**
     * Approve a review.
     */
    public function approve($id)
    {
        return $this->model->where('_id', $id)->update(['is_approved' => true]);
    }

    /**
     * Get review statistics.
     */
    public function getStats($reviewableId = null, $reviewableType = null)
    {
        $query = $this->model;
        
        if ($reviewableId && $reviewableType) {
            $query = $query->where('reviewable_id', $reviewableId)
                ->where('reviewable_type', $reviewableType);
        }

        return [
            'total' => $query->count(),
            'approved' => $query->where('is_approved', true)->count(),
            'pending' => $query->where('is_approved', false)->count(),
            'average_rating' => $query->where('is_approved', true)->avg('rating'),
        ];
    }

    /**
     * Get recent reviews.
     */
    public function getRecent($limit = 5, $approvedOnly = true)
    {
        $query = $this->model->with('user');
        
        if ($approvedOnly) {
            $query->where('is_approved', true);
        }

        return $query->latest()->limit($limit)->get();
    }
}
