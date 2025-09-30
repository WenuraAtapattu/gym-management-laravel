@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Welcome back, {{ Auth::user()->name }}!</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Quick Actions -->
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-800 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('products.index') }}" class="block p-3 bg-white rounded-md shadow hover:bg-gray-50 transition">
                                Browse Products
                            </a>
                            <a href="{{ route('orders.index') }}" class="block p-3 bg-white rounded-md shadow hover:bg-gray-50 transition">
                                View Orders
                            </a>
                            <a href="{{ route('account.show') }}" class="block p-3 bg-white rounded-md shadow hover:bg-gray-50 transition">
                                Update Profile
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-green-800 mb-4">Recent Activity</h3>
                        <div class="space-y-3">
                            <div class="p-3 bg-white rounded-md shadow">
                                <p class="text-sm text-gray-600">Welcome to your dashboard!</p>
                                <p class="text-xs text-gray-500 mt-1">Just now</p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Summary -->
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-purple-800 mb-4">Account Summary</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Member since</p>
                                <p class="font-medium">{{ Auth::user()->created_at->format('F j, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <p class="font-medium">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
