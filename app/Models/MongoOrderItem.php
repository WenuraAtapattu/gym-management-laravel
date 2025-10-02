<?php

namespace App\Models;

class MongoOrderItem extends MongoModel
{
    protected $collection = 'order_items';
    
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_type',
        'quantity',
        'price',
        'total',
    ];

    protected $casts = [
        'price' => 'float',
        'total' => 'float',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the cart that owns the order item.
     */
    public function cart()
    {
        return $this->belongsTo(MongoCart::class, 'cart_id');
    }

    /**
     * Get the product that owns the order item.
     */
    public function product()
    {
        return $this->belongsTo(MongoProduct::class, 'product_id');
    }
}