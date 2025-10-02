<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
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
        // Check if user is authenticated
        if (!Auth::check()) {
            // Store intended URL before redirecting to login
            if (!$request->is('login*', 'admin/login*')) {
                session()->put('url.intended', $request->fullUrl());
            }
            return redirect()->route('admin.login')
                ->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Verify user is active
        if (!isset($user->is_active) || !$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account is inactive. Please contact support.');
        }

        // Verify admin status
        if (!isset($user->is_admin) || !$user->is_admin) {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access the admin area.');
        }

        return $next($request);
    }
}