@php
$user = auth()->user();
$isAdmin = $user && $user->is_admin;
$currentRoute = request()->route() ? request()->route()->getName() : '';
@endphp

<nav x-data="{ mobileMenuOpen: false }" class="bg-white shadow-sm border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left: Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="Steel Gym">
                </a>
            </div>

            <!-- Center: Navigation Links - Desktop -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="nav-link {{ $currentRoute === 'home' ? 'active' : '' }}">Home</a>
                <a href="{{ route('about') }}" class="nav-link {{ $currentRoute === 'about' ? 'active' : '' }}">About</a>
                <a href="{{ route('products.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'products') ? 'active' : '' }}">Products</a>
                <a href="{{ route('contact') }}" class="nav-link {{ $currentRoute === 'contact' ? 'active' : '' }}">Contact</a>
                @auth
                    @if($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Admin Dashboard</a>
                @endif
                <a href="{{ route('account.show') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="nav-link">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
{{ ... }}
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="flex md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange-500" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" 
         class="md:hidden"
         @click.away="mobileMenuOpen = false"
         x-cloak>
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="mobile-nav-link {{ $currentRoute === 'home' ? 'active' : '' }}">Home</a>
            <a href="{{ route('about') }}" class="mobile-nav-link {{ $currentRoute === 'about' ? 'active' : '' }}">About</a>
            <a href="{{ route('products.index') }}" class="mobile-nav-link {{ str_starts_with($currentRoute, 'products') ? 'active' : '' }}">Products</a>
            <a href="{{ route('contact') }}" class="mobile-nav-link {{ $currentRoute === 'contact' ? 'active' : '' }}">Contact</a>
            @auth
                @if($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link {{ str_starts_with($currentRoute, 'admin.') ? 'active' : '' }}">Admin Dashboard</a>
                @endif
                <a href="{{ route('orders.index') }}" class="mobile-nav-link {{ str_starts_with($currentRoute, 'orders.') ? 'active' : '' }}">Orders</a>
                <a href="{{ route('account.show') }}" class="mobile-nav-link {{ str_starts_with($currentRoute, 'account.') ? 'active' : '' }}">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-left mobile-nav-link text-red-600 hover:text-red-700 hover:bg-red-50">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mobile-nav-link">Login</a>
                <a href="{{ route('register') }}" class="mobile-nav-link bg-orange-600 text-white hover:bg-orange-700">Register</a>
            @endauth
        </div>
    </div>
</nav>

<style>
    .nav-link {
        @apply text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-200;
    }
    .nav-link.active {
        @apply text-orange-600 font-semibold;
    }
    .mobile-nav-link {
        @apply block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50;
    }
    .mobile-nav-link.active {
        @apply bg-gray-50 text-orange-600;
    }
    [x-cloak] { 
        display: none !important; 
    }
</style>
