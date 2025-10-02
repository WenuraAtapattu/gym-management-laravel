@extends('layouts.admin')

@section('title', 'Data Explorer')

@section('content')
<div class="space-y-6">
    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('admin.data-explorer.index') }}" 
               class="{{ request()->routeIs('admin.data-explorer.index') ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Overview
            </a>
            <a href="{{ route('admin.data-explorer.products') }}" 
               class="{{ request()->routeIs('admin.data-explorer.products') ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Products
            </a>
            <a href="{{ route('admin.data-explorer.users') }}" 
               class="{{ request()->routeIs('admin.data-explorer.users') ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Users
            </a>
            <a href="{{ route('admin.data-explorer.orders') }}" 
               class="{{ request()->routeIs('admin.data-explorer.orders') ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Orders
            </a>
            <a href="{{ route('admin.data-explorer.carts') }}" 
               class="{{ request()->routeIs('admin.data-explorer.carts') ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Carts
            </a>
        </nav>
    </div>

    <!-- Content -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                @yield('page-title', 'Data Explorer')
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                @yield('page-description', 'Explore and manage your application data')
            </p>
        </div>
        <div class="border-t border-gray-200">
            @yield('explorer-content')
        </div>
    </div>
</div>
@endsection
