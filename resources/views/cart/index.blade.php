@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Shopping Cart</h1>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-500">
                Continue Shopping
                <span aria-hidden="true"> &rarr;</span>
            </a>
        </div>

        @if ($cart->items->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ $cart->items->count() }} {{ Str::plural('item', $cart->items->count()) }} in your cart
                    </h2>
                </div>
                
                <div class="border-t border-gray-200">
                    <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-3 bg-gray-50 text-sm font-medium text-gray-500 uppercase tracking-wider">
                        <div class="col-span-6">Product</div>
                        <div class="col-span-2 text-center">Price</div>
                        <div class="col-span-2 text-center">Quantity</div>
                        <div class="col-span-2 text-right">Total</div>
                    </div>
                    
                    @foreach ($cart->items as $item)
                        <div class="border-t border-gray-200">
                            <div class="px-4 py-6 sm:px-6">
                                <div class="md:grid md:grid-cols-12 md:gap-4">
                                    <!-- Product -->
                                    <div class="md:col-span-6 flex">
                                        <div class="flex-shrink-0 w-20 h-20 rounded-md overflow-hidden">
                                            <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : asset('images/placeholder-product.jpg') }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('products.show', $item->product) }}">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h3>
                                            @if($item->product->has_stock && $item->product->stock_quantity < $item->quantity)
                                                <p class="mt-1 text-sm text-red-600">
                                                    Only {{ $item->product->stock_quantity }} in stock
                                                </p>
                                            @endif
                                            <form action="{{ route('cart.destroy', $item->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:text-red-500">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="mt-4 md:mt-0 md:col-span-2 flex items-center">
                                        <div class="md:hidden text-sm font-medium text-gray-500">Price</div>
                                        <div class="md:mx-auto">
                                            <p class="text-sm text-gray-900">
                                                ${{ number_format($item->product->price, 2) }}
                                                @if($item->product->compare_at_price > $item->product->price)
                                                    <span class="ml-1 text-xs text-gray-500 line-through">
                                                        ${{ number_format($item->product->compare_at_price, 2) }}
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="mt-4 md:mt-0 md:col-span-2 flex items-center">
                                        <div class="md:hidden text-sm font-medium text-gray-500">Quantity</div>
                                        <div class="md:mx-auto">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                                @csrf
                                                @method('PATCH')
                                                <select name="quantity" onchange="this.form.submit()" 
                                                        class="max-w-full rounded-md border border-gray-300 py-1.5 text-left text-base font-medium leading-5 text-gray-700 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 sm:text-sm">
                                                    @for($i = 1; $i <= min(10, $item->product->stock_quantity + $item->quantity); $i++)
                                                        <option value="{{ $i }}" {{ $i == $item->quantity ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="mt-4 md:mt-0 md:col-span-2 flex items-center justify-end">
                                        <div class="md:hidden text-sm font-medium text-gray-500">Total</div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">
                                                ${{ number_format($item->product->price * $item->quantity, 2) }}
                                            </p>
                                            @if($item->product->compare_at_price > $item->product->price)
                                                @php
                                                    $saved = ($item->product->compare_at_price - $item->product->price) * $item->quantity;
                                                    $discount = (($item->product->compare_at_price - $item->product->price) / $item->product->compare_at_price) * 100;
                                                @endphp
                                                <p class="mt-1 text-xs text-green-700">
                                                    Save ${{ number_format($saved, 2) }} ({{ round($discount) }}%)
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="border-t border-gray-200 px-4 py-6 sm:px-6">
                    <div class="flex justify-between text-base font-medium text-gray-900">
                        <p>Subtotal</p>
                        <p>${{ number_format($subtotal, 2) }}</p>
                    </div>
                    <p class="mt-0.5 text-sm text-gray-500">Shipping and taxes calculated at checkout.</p>
                    <div class="mt-6">
                        <a href="{{ route('checkout.index') }}" class="flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-orange-600 hover:bg-orange-700">
                            Checkout
                        </a>
                    </div>
                    <div class="mt-6 flex justify-center text-sm text-center text-gray-500">
                        <p>
                            or
                            <a href="{{ route('products.index') }}" class="ml-1 font-medium text-orange-600 hover:text-orange-500">
                                Continue Shopping<span aria-hidden="true"> &rarr;</span>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center bg-white py-12 px-4 shadow sm:px-6 sm:rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Your cart is empty</h3>
                <p class="mt-1 text-gray-500">Start adding some items to your cart.</p>
                <div class="mt-6">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700">
                        Browse Products
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
