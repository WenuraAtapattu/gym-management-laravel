<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Review extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reviewable_id',
        'reviewable_type',
        'user_id',
        'rating',
        'title',
        'comment',
        'content', // For backward compatibility with tests
        'is_approved',
        'guest_name',
        'guest_email',
    ];


    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('approved', function (Builder $builder) {
            $request = request();
            $user = $request ? $request->user() : null;
            if (!app()->runningInConsole() && ($user === null || !$user->is_admin)) {
                $builder->where('is_approved', true);
            }
        });
    }

    /**
     * Get the content attribute (alias for comment for backward compatibility).
     */
    public function getContentAttribute()
    {
        return $this->comment;
    }

    /**
     * Set the content attribute (alias for comment for backward compatibility).
     */
    public function setContentAttribute($value)
    {
        $this->attributes['comment'] = $value;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent reviewable model (product or other).
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the product that owns the review.
     * @deprecated Use reviewable() instead
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'reviewable_id')
            ->where('reviewable_type', Product::class);
    }

    /**
     * Get the user that made the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all media for the review.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * Get all reports for the review.
     */
    public function reports()
    {
        return $this->hasMany(\App\Models\Report::class);
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query, bool $approved = true)
    {
        return $query->where('is_approved', $approved);
    }

    /**
     * Scope a query to only include pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope a query to only include reviews for a specific product.
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('reviewable_id', $productId)
            ->where('reviewable_type', Product::class);
    }

    /**
     * Get the user's name for the review.
     */
    public function getReviewerNameAttribute(): string
    {
        if ($this->user_id && $this->relationLoaded('user')) {
            return $this->user->getAttribute('name') ?: 'Anonymous';
        }
        return (string) ($this->getAttribute('guest_name') ?? 'Anonymous');
    }
}
