<?php

namespace App\Models;

use App\Models\MongoModel;
use App\Models\MongoUser;
use App\Models\MongoOrderItem;

class MongoCart extends MongoModel
{
    protected $collection = 'carts';

    protected $fillable = [
        'user_id',
        'items',
        'total',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user()
    {
        return $this->belongsTo(MongoUser::class, 'user_id');
    }

    /**
     * Get the items for the cart.
     */
    public function items()
    {
        return $this->hasMany(MongoOrderItem::class, 'cart_id');
    }
}