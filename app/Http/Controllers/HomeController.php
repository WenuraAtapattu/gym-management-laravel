<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Get featured products
            $featuredProducts = \App\Models\Product::withCount(['reviews' => function($query) {
                    $query->where('is_approved', true);
                }])
                ->withAvg(['reviews' => function($query) {
                    $query->where('is_approved', true);
                }], 'rating')
                ->where('is_featured', true)
                ->where('is_active', true)
                ->limit(8)
                ->get();

            // Get other data needed for the home page
            $categories = \App\Models\Category::all();
            $testimonials = \App\Models\Testimonial::where('is_approved', true)->get();
            
            return view('home', compact('featuredProducts', 'categories', 'testimonials'));

        } catch (\Exception $e) {
            Log::error('Error in HomeController: ' . $e->getMessage());
            return response()->view('errors.maintenance', [
                'message' => 'We are currently performing maintenance. Please check back soon.'
            ], 503);
        }
    }
}
