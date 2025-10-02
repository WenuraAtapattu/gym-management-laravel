<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            if ($request->expectsJson()) {
                return \Illuminate\Support\Facades\Response::json(['message' => 'Please log in to access this page.'], 401);
            }
            return \Illuminate\Support\Facades\Redirect::route('login')->with('error', 'Please log in to access this page.');
        }

        if (!\Illuminate\Support\Facades\Auth::user()->is_admin) {
            if ($request->expectsJson()) {
                return \Illuminate\Support\Facades\Response::json(['message' => 'You do not have permission to access this area.'], 403);
            }
            
            return \Illuminate\Support\Facades\Redirect::route('home')
                ->with('error', 'You do not have permission to access the admin area.');
        }

        return $next($request);
    }
}
