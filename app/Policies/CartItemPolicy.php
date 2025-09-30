<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the cart item.
     * 
     * @param User $user
     * @param CartItem $cartItem
     * @return bool
     */
    public function update(User $user, CartItem $cartItem): bool
    {
        // Load the cart relationship if not already loaded
        if (!$cartItem->relationLoaded('cart')) {
            $cartItem->load('cart');
        }
        
        return $cartItem->cart && $user->getKey() === $cartItem->cart->getAttribute('user_id');
    }

    /**
     * Determine whether the user can delete the cart item.
     * 
     * @param User $user
     * @param CartItem $cartItem
     * @return bool
     */
    public function delete(User $user, CartItem $cartItem): bool
    {
        // Load the cart relationship if not already loaded
        if (!$cartItem->relationLoaded('cart')) {
            $cartItem->load('cart');
        }
        
        return $cartItem->cart && $user->getKey() === $cartItem->cart->getAttribute('user_id');
    }
}