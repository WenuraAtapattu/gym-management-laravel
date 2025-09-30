@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="pt-24">
        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb">
            <ol role="list" class="flex max-w-2xl px-4 mx-auto space-x-2 sm:px-6 lg:max-w-7xl lg:px-8">
                <li>
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="mr-2 text-sm font-medium text-gray-900">Home</a>
                        <svg width="16" height="20" viewBox="0 0 16 20" fill="currentColor" class="w-4 h-5 text-gray-300">
                            <path d="M5.697 4.34L8.98 16.532h1.327L7.025 4.341H5.697z" />
                        </svg>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <a href="{{ route('products.index') }}" class="mr-2 text-sm font-medium text-gray-900">Products</a>
                        <svg width="16" height="20" viewBox="0 0 16 20" fill="currentColor" class="w-4 h-5 text-gray-300">
                            <path d="M5.697 4.34L8.98 16.532h1.327L7.025 4.341H5.697z" />
                        </svg>
                    </div>
                </li>
                <li class="text-sm">
                    <a href="#" aria-current="page" class="font-medium text-gray-500 hover:text-gray-600">{{ $product->name }}</a>
                </li>
            </ol>
        </nav>

        <!-- Product info -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="lg:grid lg:grid-cols-2 lg:gap-8">
            <!-- Product image -->
            <div class="lg:col-span-1">
                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-100">
                    <img 
                        src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/placeholder-product.jpg') }}" 
                        alt="{{ $product->name }}" 
                        class="h-full w-full object-contain object-center"
                        loading="lazy"
                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder-product.jpg') }}'"
                    >
                </div>
                @if($product->gallery_images)
                    <div class="mt-4 grid grid-cols-4 gap-4">
                        @foreach(json_decode($product->gallery_images) as $image)
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-100">
                                <img 
                                    src="{{ asset('storage/' . $image) }}" 
                                    alt="{{ $product->name }} - Gallery image {{ $loop->iteration }}" 
                                    class="h-full w-full object-cover object-center cursor-pointer hover:opacity-75"
                                    onclick="document.querySelector('.main-product-image').src = this.src"
                                    loading="lazy"
                                >
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product details -->
            <div class="lg:col-span-1 lg:pl-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $product->name }}</h1>
                @if($product->is_featured)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mt-2">
                        Featured
                    </span>
                @endif

                <!-- Price and add to cart -->
                <div class="mt-6">
                <h2 class="sr-only">Product information</h2>
                <div class="flex items-baseline">
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</p>
                        @if($product->compare_at_price > $product->price)
                            <span class="ml-2 text-lg text-gray-500 line-through">${{ number_format($product->compare_at_price, 2) }}</span>
                            @php
                                $discount = (($product->compare_at_price - $product->price) / $product->compare_at_price) * 100;
                            @endphp
                            <span class="ml-2 text-sm font-medium text-green-700">
                                Save {{ round($discount) }}%
                            </span>
                        @endif
                    </div>

                <!-- Reviews -->
                <div class="mt-6">
                    <h3 class="sr-only">Reviews</h3>
                    <div class="flex items-center">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->reviews_avg_rating ?? 0))
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <p class="ml-2 text-sm text-gray-500">{{ $product->reviews_count ?? 0 }} reviews</p>
                    </div>
                </div>

                <form class="mt-10" action="{{ route('cart.store', $product) }}" method="POST">
                    @csrf
                    <!-- Quantity -->
                    <div class="mt-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-900">Quantity</h3>
                        </div>
                        <div class="mt-4">
                            <select name="quantity" class="max-w-full rounded-md border border-gray-300 py-1.5 text-left text-base font-medium leading-5 text-gray-700 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 sm:text-sm">
                                @for($i = 1; $i <= min(10, $product->stock_quantity); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="flex items-center justify-center w-full px-8 py-3 mt-10 text-base font-medium text-white bg-orange-600 border border-transparent rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Add to cart
                    </button>
                </form>
            </div>

            <div class="mt-10">
                <!-- Description and details -->
                <div>
                    <h3 class="sr-only">Description</h3>

                    <div class="space-y-6">
                        <p class="text-base text-gray-900">{{ $product->description }}</p>
                    </div>
                </div>

                <div class="mt-10">
                    <h3 class="text-sm font-medium text-gray-900">Highlights</h3>

                    <div class="mt-4">
                        <ul role="list" class="pl-4 space-y-2 text-sm list-disc">
                            @if($product->features)
                                @foreach(explode('\n', $product->features) as $feature)
                                    <li class="text-gray-400">
                                        <span class="text-gray-600">{{ trim($feature) }}</span>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="mt-10">
                    <h2 class="text-sm font-medium text-gray-900">Details</h2>

                    <div class="mt-4 space-y-6">
                        <p class="text-sm text-gray-600">{{ $product->details ?? 'No additional details available.' }}</p>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>

        <!-- Related products -->
        @if($relatedProducts->isNotEmpty())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">You may also like</h2>
                <div class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                    @foreach($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
