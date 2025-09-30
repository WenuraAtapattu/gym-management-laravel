@extends('layouts.app')

@section('content')
<div class="bg-gray-50">
    <!-- Hero Section -->
    <div class="relative py-16 bg-gradient-to-r from-orange-500 to-amber-600">
        <div class="overflow-hidden absolute inset-0">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1601422407692-ec4eeec1d9b3?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80')] bg-cover bg-center opacity-30"></div>
        </div>
        <div class="relative px-4 mx-auto max-w-7xl text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Our Products
            </h1>
            <p class="mx-auto mt-6 max-w-lg text-xl text-orange-100">
                High-quality fitness equipment and supplements to support your fitness journey
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Featured Products -->
        @if($featuredProducts->isNotEmpty())
            <div class="mb-12">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
                    <a href="{{ route('products.featured') }}" class="font-medium text-orange-600 hover:text-orange-500">
                        View all featured
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                    @foreach($featuredProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        @endif

        <!-- All Products -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">All Products</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <label for="sort" class="sr-only">Sort</label>
                        <select id="sort" name="sort" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="newest">Newest</option>
                            <option value="price_low_high">Price: Low to High</option>
                            <option value="price_high_low">Price: High to Low</option>
                            <option value="name_asc">Name: A to Z</option>
                            <option value="name_desc">Name: Z to A</option>
                        </select>
                    </div>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="py-12 text-center">
                    <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4.5L4 7m16 0l-8 4.5M4 7v10l8 4.5m0 0l8-4.5M4 7v10l8 4.5m0 0l8-4.5m0 0V7m-8 4.5L4 7" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter to find what you're looking for.</p>
                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-md border border-transparent shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Clear all filters
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Newsletter Section -->
    <div class="bg-white">
        <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:py-16 lg:px-8">
            <div class="px-6 py-6 bg-orange-600 rounded-lg md:py-12 md:px-12 lg:py-16 lg:px-16 xl:flex xl:items-center">
                <div class="xl:w-0 xl:flex-1">
                    <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                        Want product news and updates?
                    </h2>
                    <p class="mt-3 max-w-3xl text-lg leading-6 text-orange-100">
                        Sign up for our newsletter to stay up to date.
                    </p>
                </div>
                <div class="mt-8 sm:w-full sm:max-w-md xl:mt-0 xl:ml-8">
                    <form class="sm:flex">
                        <label for="email-address" class="sr-only">Email address</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required class="px-5 py-3 w-full placeholder-gray-500 rounded-md border-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-orange-600" placeholder="Enter your email">
                        <button type="submit" class="flex justify-center items-center px-5 py-3 mt-3 w-full text-base font-medium text-orange-600 bg-white rounded-md border border-transparent shadow hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-orange-600 sm:mt-0 sm:ml-3 sm:w-auto sm:flex-shrink-0">
                            Notify me
                        </button>
                    </form>
                    <p class="mt-3 text-sm text-orange-100">
                        We care about the protection of your data. Read our
                        <a href="#" class="font-medium text-white underline">
                            Privacy Policy.
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle sorting
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.getElementById('sort');
        
        function updateProducts() {
            const params = new URLSearchParams(window.location.search);
            
            if (sortSelect.value) {
                params.set('sort', sortSelect.value);
            } else {
                params.delete('sort');
            }
            
            window.location.href = window.location.pathname + '?' + params.toString();
        }
        
        sortSelect?.addEventListener('change', updateProducts);
        
        // Set initial value from URL
        const urlParams = new URLSearchParams(window.location.search);
        if (sortSelect && urlParams.has('sort')) {
            sortSelect.value = urlParams.get('sort');
        }
    });
</script>
@endpush
@endsection
