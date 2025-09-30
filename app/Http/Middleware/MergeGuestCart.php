<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MergeGuestCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only merge carts for authenticated users who just logged in
        if (Auth::check() && $this->isLoginRequest($request)) {
            Cart::mergeCartsOnLogin(Auth::user());
        }

        return $response;
    }

    /**
     * Check if the request is for login
     */
    protected function isLoginRequest(Request $request): bool
    {
        return $request->is('login') && $request->isMethod('post');
    }
}
