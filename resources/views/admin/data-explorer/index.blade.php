@extends('admin.data-explorer.layout')

@section('page-title', 'Data Explorer Overview')

@section('explorer-content')
<div class="bg-white overflow-hidden">
    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Products Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Products
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ number_format(\App\Models\Product::count()) }}
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        <a href="{{ route('admin.data-explorer.products') }}" class="text-orange-600 hover:text-orange-900">View all</a>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Users
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ number_format(\App\Models\User::count()) }}
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        <a href="{{ route('admin.data-explorer.users') }}" class="text-orange-600 hover:text-orange-900">View all</a>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Orders
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ number_format(\App\Models\Order::count()) }}
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        <a href="{{ route('admin.data-explorer.orders') }}" class="text-orange-600 hover:text-orange-900">View all</a>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carts Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Active Carts
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ number_format(\App\Models\Cart::count()) }}
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        <a href="{{ route('admin.data-explorer.carts') }}" class="text-orange-600 hover:text-orange-900">View all</a>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Activity</h3>
            <div class="mt-4">
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        @php
                            $recentOrders = \App\Models\Order::with('user')
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @forelse($recentOrders as $order)
                            <li>
                                <a href="{{ route('admin.data-explorer.orders') }}" class="block hover:bg-gray-50">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-orange-600 truncate">
                                                Order #{{ $order->order_number }}
                                            </p>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($order->status) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-2 sm:flex sm:justify-between">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $order->user->name ?? 'Guest' }}
                                                </p>
                                                <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $order->billing_address['city'] ?? 'N/A' }}, {{ $order->billing_address['country'] ?? 'N/A' }}
                                                </p>
                                            </div>
                                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                </svg>
                                                <p>
                                                    <time datetime="{{ $order->created_at->toDateString() }}">
                                                        {{ $order->created_at->diffForHumans() }}
                                                    </time>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="p-4 text-center text-gray-500">
                                No recent orders found.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
