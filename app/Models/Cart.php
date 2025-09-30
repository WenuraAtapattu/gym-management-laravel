<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    /**
     * Get the items in the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Merge guest cart items into user's cart upon login
     */
    public static function mergeCartsOnLogin($user)
    {
        $guestCart = self::where('session_id', session()->getId())->first();
        $userCart = self::firstOrCreate(['user_id' => $user->id]);

        if ($guestCart) {
            foreach ($guestCart->items as $item) {
                $userCart->items()->updateOrCreate(
                    ['product_id' => $item->product_id],
                    ['quantity' => $item->quantity]
                );
            }
            $guestCart->delete();
        }
    }
}