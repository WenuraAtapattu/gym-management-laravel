@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    /* Stats Cards */
    .stat-card {
        @apply bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200;
    }
    
    .stat-icon {
        @apply p-2.5 rounded-lg w-12 h-12 flex items-center justify-center mb-3;
    }
    
    .stat-value {
        @apply text-2xl font-bold text-gray-900;
    }
    
    .stat-label {
        @apply text-sm font-medium text-gray-500 mt-1;
    }
    
    .trend-up { @apply text-green-600 bg-green-50; }
    .trend-down { @apply text-red-600 bg-red-50; }
    
    /* Tables */
    .table-container { @apply bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden; }
    .table-header { @apply px-6 py-4 border-b border-gray-100 bg-gray-50; }
    .table-header h3 { @apply text-lg font-medium text-gray-900; }
    .table-body { @apply divide-y divide-gray-100; }
    .table-row { @apply hover:bg-gray-50 transition-colors duration-150; }
    .table-cell { @apply px-6 py-4 whitespace-nowrap text-sm text-gray-700; }
    
    /* Buttons */
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200;
    }
    
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200;
    }
    
    /* Badges */
    .badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium; }
    .badge-success { @apply bg-green-100 text-green-800; }
    .badge-warning { @apply bg-yellow-100 text-yellow-800; }
    .badge-danger { @apply bg-red-100 text-red-800; }
    
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
@endpush

@section('content')
<div x-data="adminDashboard" class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex-1">
            <div class="flex gap-3 items-center">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
                <span class="px-2.5 py-0.5 text-xs font-medium text-orange-800 bg-orange-100 rounded-full">
                    Admin
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500">Welcome back, {{ Auth::user()->name }}! Here's what's happening with your store.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.products.create') }}" class="btn-primary">
                <svg class="mr-2 -ml-1 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Product
            </a>
            <button @click="printReport" class="btn-secondary">
                <svg class="mr-2 -ml-1 w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Generate Report
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Revenue -->
        <div class="stat-card group">
            <div class="flex justify-between items-center">
                <div class="text-blue-600 bg-blue-50 transition-colors stat-icon group-hover:bg-blue-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-right">
                    @php
                        $revenueChange = $stats['revenue_change'] ?? 0;
                        $isPositive = $revenueChange >= 0;
                    @endphp
                    <span class="text-{{ $isPositive ? 'green' : 'red' }}-500 text-xs font-medium bg-{{ $isPositive ? 'green' : 'red' }}-50 px-2 py-1 rounded-full flex items-center justify-end">
                        @if($isPositive)
                            <svg class="mr-0.5 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg class="mr-0.5 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @endif
                        {{ number_format(abs($revenueChange), 1) }}% from last period
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">${{ number_format($stats['revenue'], 2) }}</p>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="stat-card group">
            <div class="flex justify-between items-center">
                <div class="text-green-600 bg-green-50 transition-colors stat-icon group-hover:bg-green-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="text-right">
                    <span class="flex justify-end items-center px-2 py-1 text-xs font-medium text-green-500 bg-green-50 rounded-full">
                        <svg class="mr-0.5 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        +8% from last month
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
            </div>
        </div>

        <!-- Products -->
        <div class="stat-card group">
            <div class="flex justify-between items-center">
                <div class="text-purple-600 bg-purple-50 transition-colors stat-icon group-hover:bg-purple-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="text-right">
                    <span class="flex justify-end items-center px-2 py-1 text-xs font-medium text-green-500 bg-green-50 rounded-full">
                        <svg class="mr-0.5 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        +12% from last month
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Products</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
            </div>
        </div>

        <!-- Customers -->
        <div class="stat-card group">
            <div class="flex justify-between items-center">
                <div class="text-yellow-600 bg-yellow-50 transition-colors stat-icon group-hover:bg-yellow-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="text-right">
                    <span class="flex justify-end items-center px-2 py-1 text-xs font-medium text-green-500 bg-green-50 rounded-full">
                        <svg class="mr-0.5 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        +5% from last month
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Customers</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
            </div>
        </div>
    </div>

    <!-- Admin Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Admin Account</h3>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Profile Information -->
            <div class="bg-gray-50 p-5 rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-900">Profile Information</h4>
                    <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">Update your account's profile information and email address.</p>
                <a href="{{ route('account.edit') }}" class="inline-flex items-center text-sm font-medium text-orange-600 hover:text-orange-500 transition-colors">
                    Edit Profile
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            
            <!-- Update Password -->
            <div class="bg-gray-50 p-5 rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-900">Update Password</h4>
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">Ensure your account is using a long, random password to stay secure.</p>
                <a href="{{ route('account.password.edit') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors">
                    Update Password
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 gap-6 mt-8 md:grid-cols-2">
        <!-- Products Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Products Management</h3>
                    <div class="flex-shrink-0 p-2 text-orange-500 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="mb-4 text-sm text-gray-600">Manage your store's products, inventory, and pricing in one place.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        View All Products
                    </a>
                    <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add New Product
                    </a>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Users Management</h3>
                    <div class="flex-shrink-0 p-2 text-blue-500 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="mb-4 text-sm text-gray-600">Manage user accounts, roles, and permissions.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        View All Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="mt-8 table-container">
        <div class="flex justify-between items-center table-header">
            <h3>Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-orange-600 transition-colors hover:text-orange-500">
                View all orders →
            </a>
        </div>
        
        <div class="table-body">
            @forelse($recentOrders as $order)
                <div class="table-row">
                    <div class="flex items-center px-6 py-4">
                        <div class="flex flex-shrink-0 justify-center items-center w-10 h-10 text-blue-600 bg-blue-50 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                Order #{{ $order->id }}
                            </div>
                            <div class="flex items-center mt-1 text-sm text-gray-500">
                                <span>{{ $order->user->name }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="ml-auto text-right">
                            <div class="text-sm font-semibold text-gray-900">
                                ${{ number_format($order->total, 2) }}
                            </div>
                            <div class="mt-1">
                                @if($order->status === 'completed')
                                    <span class="badge badge-success">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @elseif($order->status === 'processing')
                                    <span class="badge badge-warning">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No orders</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new order.</p>
                    <div class="mt-6">
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 text-sm btn-primary">
                            <svg class="mr-2 -ml-1 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            New Order
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">View all orders</a>
        </div>
    </div>
</div>
@endsection
