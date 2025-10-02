<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MongoReview extends MongoModel
{
    protected $collection = 'reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'comment',
        'status',
        'guest_name',
        'guest_email'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    /**
     * Get the parent reviewable model.
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include guest reviews.
     */
    public function scopeGuest($query)
    {
        return $query->whereNull('user_id');
    }
}