@extends('layouts.original')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Welcome Section -->
    <div class="bg-blue-600 text-white rounded-lg p-8 mb-8">
        @auth
            <h1 class="text-4xl font-bold mb-4">Welcome back, {{ $user->name ?? 'Valued Member' }}!</h1>
            <p class="text-xl mb-6">Your fitness journey continues here</p>
            
            <!-- User Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-white/20 rounded-lg p-4">
                    <h3 class="text-lg font-semibold">Total Orders</h3>
                    <p class="text-2xl font-bold">{{ $userStats['total_orders'] ?? 0 }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-4">
                    <h3 class="text-lg font-semibold">Member Since</h3>
                    <p class="text-2xl font-bold">{{ $user->created_at->format('M Y') ?? 'N/A' }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-4">
                    <h3 class="text-lg font-semibold">Account Status</h3>
                    <p class="text-2xl font-bold text-green-300">Active</p>
                </div>
            </div>
        @else
            <h1 class="text-4xl font-bold mb-4">Welcome to Our Gym</h1>
            <p class="text-xl mb-6">Start your fitness journey today</p>
            <div class="mt-6">
                <a href="{{ route('login') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-200">
                    Sign In / Register
                </a>
            </div>
        @endauth
    </div>

    <!-- Featured Products -->
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
            <a href="{{ route('products.featured') }}" class="font-medium text-orange-600 hover:text-orange-500">
                View all featured
                <span aria-hidden="true"> &rarr;</span>
            </a>
        </div>
        @if(isset($featuredProducts) && $featuredProducts->count() > 0)
            <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">No featured products available at the moment.</p>
            </div>
        @endif
    </div>

    <!-- Recent Orders Section -->
    @if($userStats['recent_orders']->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">Your Recent Orders</h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Order History</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($userStats['recent_orders'] as $order)
                        <div class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Order #{{ $order->id }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">${{ number_format($order->total_amount ?? 0, 2) }}</p>
                                    @php
                                        $statusClasses = [
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'processing' => 'bg-amber-100 text-amber-800',
                                            'default' => 'bg-rose-100 text-rose-800'
                                        ];
                                        $statusClass = $statusClasses[strtolower($order->status)] ?? $statusClasses['default'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ ucfirst($order->status ?? 'Pending') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('orders.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        View all orders →
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- About Section -->
    <div class="mt-16">
        <h2 class="text-2xl font-bold mb-6">Why Choose Us?</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-blue-600 text-4xl mb-3">💪</div>
                <h3 class="font-semibold text-lg mb-2">Expert Trainers</h3>
                <p class="text-gray-600">Our certified trainers are here to help you achieve your fitness goals.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-blue-600 text-4xl mb-3">🏋️</div>
                <h3 class="font-semibold text-lg mb-2">Modern Equipment</h3>
                <p class="text-gray-600">State-of-the-art equipment for all your workout needs.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-blue-600 text-4xl mb-3">❤️</div>
                <h3 class="font-semibold text-lg mb-2">Supportive Community</h3>
                <p class="text-gray-600">Join a community that motivates and supports your fitness journey.</p>
            </div>
        </div>
    </div>
</div>
@endsection
