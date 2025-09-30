<?php

namespace App\Models;

class MongoProduct extends MongoModel
{
    protected $collection = 'products';
    
    protected $fillable = [
        'name', 'description', 'price', 'stock', 'category_id',
        'image', 'status', 'created_at', 'updated_at',
        // Additional fields from your MySQL products table
        'brand', 'compare_at_price', 'cost_per_item', 'barcode',
        'is_featured', 'is_bestseller', 'is_new_arrival',
        'seo_title', 'seo_description', 'seo_keywords'
    ];
    
    protected $casts = [
        'price' => 'float',
        'compare_at_price' => 'float',
        'cost_per_item' => 'float',
        'stock' => 'integer',
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Eager load category by default
    protected $with = ['category'];
    
    // Category relationship
    public function category()
    {
        return $this->belongsTo(MongoCategory::class, 'category_id');
    }
    
    // Add any MongoDB-specific methods here
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
    
    // Convert price to cents for storage (optional)
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = round(floatval($value), 2);
    }
}