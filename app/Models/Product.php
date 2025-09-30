<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use App\Models\Review;
use App\Models\Category;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'brand',
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'cost_per_item',
        'barcode',
        'image',
        'stock_quantity',
        'has_stock',
        'is_featured',
        'is_bestseller',
        'is_new_arrival',
        'is_active',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_per_item' => 'decimal:2',
        'has_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'formatted_price',
        'formatted_compare_at_price',
        'image_url',
        'is_in_stock',
        'is_on_sale',
        'savings_percentage',
        'formatted_savings',
    ];

    /**
     * Get the full URL for the product image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return asset('images/placeholder-product.png');
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->getOriginal('slug'))) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Get the formatted compare at price attribute.
     */
    public function getFormattedCompareAtPriceAttribute(): ?string
    {
        return $this->compare_at_price ? number_format((float) $this->compare_at_price, 2) : null;
    }

    /**
     * Check if the product is on sale.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_at_price && $this->compare_at_price > $this->price;
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->on_sale) {
            return null;
        }

        return (int) round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
    }

    /**
     * Alias for discount_percentage for backward compatibility.
     */
    public function getSavingsPercentageAttribute(): ?int
    {
        return $this->discount_percentage;
    }

    /**
     * Get the formatted savings amount.
     */
    public function getFormattedSavingsAttribute(): ?string
    {
        if (!$this->on_sale) {
            return null;
        }
        
        $savings = $this->compare_at_price - $this->price;
        return number_format($savings, 2);
    }

    /**
     * Check if the product is in stock.
     */
    public function getInStockAttribute(): bool
    {
        if (!$this->has_stock) {
            return true; // Products without stock management are always in stock
        }
        return $this->stock_quantity > 0;
    }

    /**
     * Alias for in_stock for backward compatibility.
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->in_stock;
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all reviews for the product.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include bestsellers.
     */
    public function scopeBestsellers($query)
    {
        return $query->where('is_bestseller', true);
    }

    /**
     * Scope a query to only include new arrivals.
     */
    public function scopeNewArrivals($query)
    {
        return $query->where('is_new_arrival', true);
    }

    /**
     * Scope a query to only include products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('has_stock', false)
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    /**
     * Scope a query to search products by name or description.
     */
    public function scopeSearch($query, string $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('description', 'like', "%{$searchTerm}%")
              ->orWhere('sku', 'like', "%{$searchTerm}%")
              ->orWhere('barcode', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Decrease the stock quantity.
     */
    public function decreaseStock(int $quantity = 1): bool
    {
        if (!$this->has_stock) {
            return true;
        }

        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);
        return true;
    }

    /**
     * Increase the stock quantity.
     */
    public function increaseStock(int $quantity = 1): bool
    {
        if (!$this->has_stock) {
            return true;
        }

        $this->increment('stock_quantity', $quantity);
        return true;
    }
}
