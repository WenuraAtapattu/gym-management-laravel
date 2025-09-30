@props(['product'])

<div class="overflow-hidden bg-white rounded-lg shadow-sm transition-all duration-300 transform hover:shadow-md hover:-translate-y-0.5 border border-gray-100 flex flex-col h-full">
    <!-- Product Image -->
    <div class="relative bg-white group overflow-hidden" style="height: 250px;">
        <a href="{{ route('products.show', $product) }}" class="block w-full h-full">
            <div class="w-full h-full flex items-center justify-center bg-gray-50 p-4">
                <img 
                    src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/placeholder-product.jpg') }}" 
                    alt="{{ $product->name }}" 
                    class="max-w-full max-h-full object-contain transition-transform duration-300 group-hover:scale-105"
                    style="width: auto; height: auto; max-width: 100%; max-height: 100%;"
                    loading="lazy"
                    onerror="this.onerror=null; this.src='{{ asset('images/placeholder-product.jpg') }}'"
                >
            </div>
            @if($product->is_featured)
                <div class="absolute top-2 right-2 px-2 py-1 text-xs font-bold text-white bg-orange-500 rounded-full">
                    Featured
                </div>
            @endif
        </a>
    </div>

    <!-- Product Details -->
    <div class="p-4 flex-grow flex flex-col">
        <!-- Category -->
        @if($product->category)
            <div class="mb-1">
                <span class="inline-block px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                    {{ $product->category->name }}
                </span>
            </div>
        @endif
        <h3 class="text-base font-semibold text-gray-900 line-clamp-2 mb-2">{{ $product->name }}</h3>
        
        <!-- Price -->
        <div class="mt-2">
            <div class="flex items-baseline">
                <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                @if($product->compare_at_price > $product->price)
                    <span class="ml-2 text-sm text-gray-500 line-through">${{ number_format($product->compare_at_price, 2) }}</span>
                @endif
            </div>
            @if($product->compare_at_price > $product->price)
                @php
                    $discount = (($product->compare_at_price - $product->price) / $product->compare_at_price) * 100;
                @endphp
                <span class="inline-block mt-1 text-xs font-medium text-green-700">
                    Save {{ round($discount) }}%
                </span>
            @endif
        </div>

        <!-- Reviews -->
                <!-- Reviews -->
        <div class="flex items-center mt-2">
            <div class="flex items-center">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($product->reviews_avg_rating ?? 0))
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @elseif($i == ceil($product->reviews_avg_rating ?? 0) && (fmod($product->reviews_avg_rating ?? 0, 1) > 0))
                        <div class="relative">
                            <div class="absolute overflow-hidden" style="width: {{ (fmod($product->reviews_avg_rating, 1) * 100) }}%">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <svg class="w-4 h-4 text-gray-200" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    @else
                        <svg class="w-4 h-4 text-gray-200" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endif
                @endfor
                <span class="ml-1 text-xs text-gray-500">({{ $product->reviews_count ?? 0 }})</span>
            </div>
        </div>

        <!-- Stock Status -->
        @if($product->has_stock)
            @if($product->stock_quantity > 0)
                <div class="mt-2 text-sm text-green-600">
                    <i class="mr-1 fas fa-check-circle"></i> In Stock ({{ $product->stock_quantity }} available)
                </div>
            @else
                <div class="mt-2 text-sm text-red-600">
                    <i class="mr-1 fas fa-times-circle"></i> Out of Stock
                </div>
            @endif
        @endif

        <!-- Add to Cart Button -->
                <!-- Actions -->
        <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-100">
            <a href="{{ route('products.show', $product) }}" 
               class="text-sm font-medium text-orange-600 transition-colors hover:text-orange-500">
                View Details
            </a>
            
            <form action="{{ route('cart.store', $product) }}" method="POST" class="inline-block">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" 
                        class="px-3 py-1.5 text-sm font-medium text-white bg-orange-600 rounded-md transition-colors hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        {{ $product->has_stock && $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                    <i class="mr-1 fas fa-shopping-cart"></i> Add
                </button>
            </form>
        </div>

        <!-- Quick Actions -->
        <div class="flex justify-between items-center pt-3 mt-3 text-sm text-gray-500 border-t border-gray-100">
            <button class="transition-colors hover:text-orange-500">
                <i class="far fa-heart"></i> Wishlist
            </button>
            <span>•</span>
            <button class="transition-colors hover:text-orange-500">
                <i class="fas fa-share-alt"></i> Share
            </button>
            <span>•</span>
            <button class="transition-colors hover:text-orange-500">
                <i class="fas fa-exchange-alt"></i> Compare
            </button>
        </div>
    </div>
</div>
