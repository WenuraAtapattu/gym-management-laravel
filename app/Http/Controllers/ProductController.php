<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('is_active', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Apply search query
        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        switch ($request->query('sort')) {
            case 'price_low_high':
                $query->orderBy('price');
                break;
            case 'price_high_low':
                $query->orderByDesc('price');
                break;
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'name_desc':
                $query->orderByDesc('name');
                break;
            case 'top_rated':
                $query->orderByDesc('reviews_avg_rating');
                break;
            default:
                $query->latest();
                break;
        }

        // Get featured products (for the featured section)
        $featuredProducts = (clone $query)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        // Paginate the results
        $products = $query->paginate(12)->withQueryString();

        // Get active categories
        $categories = \App\Models\Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('products.index', [
            'products' => $products,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories
        ]);
    }

    public function featured()
    {
        // First, get the featured products
        $products = Product::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->paginate(12);

        // Manually load reviews count and average rating for each product
        $products->each(function ($product) {
            $reviews = \App\Models\MongoDB\Review::where('reviewable_id', (string) $product->id)
                ->where('reviewable_type', 'App\\Models\\Product')
                ->where('is_approved', true)
                ->get();
            
            $product->reviews_count = $reviews->count();
            $product->reviews_avg_rating = $reviews->avg('rating') ?? 0;
        });

        return view('products.featured', [
            'products' => $products,
            'title' => 'Featured Products',
        ]);
    }

    public function show(Product $product)
    {
        // Eager load relationships
        $product->load(['category', 'reviews.user']);
        
        // Get related products (same category, excluding current product)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->take(4)
            ->get();

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|min:2',
        ]);

        $query = $request->input('query');
        
        $products = Product::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->where('is_active', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.search', [
            'products' => $products,
            'query' => $query
        ]);
    }
}
