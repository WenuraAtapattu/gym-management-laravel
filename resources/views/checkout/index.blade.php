@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Checkout</h1>

            <form action="{{ route('checkout.store') }}" method="POST" class="bg-white shadow-sm rounded-lg">
                @csrf
                
                <!-- Order Summary -->
                <div class="px-6 py-5 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                    <div class="mt-4 space-y-4">
                        @foreach($cart->items as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : asset('images/placeholder-product.jpg') }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="h-16 w-16 object-cover rounded">
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $item->product->name }}</h3>
                                        <p class="mt-1 text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-900">${{ number_format($item->product->price * $item->quantity, 2) }}</p>
                            </div>
                        @endforeach
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Subtotal</dt>
                                <dd class="text-sm font-medium text-gray-900">${{ number_format($subtotal, 2) }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="px-6 py-5 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Shipping Information</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                   value="{{ old('full_name', auth()->user()->name ?? '') }}">
                            @error('full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                   value="{{ old('email', auth()->user()->email ?? '') }}">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                   value="{{ old('phone') }}">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700">Shipping Address</label>
                            <textarea name="shipping_address" id="shipping_address" rows="3" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                    placeholder="Enter your complete shipping address"
                                    required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="px-6 py-5 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Payment Information</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="text" id="card_number" name="card_number" required
                                       class="block w-full border border-gray-300 rounded-md py-2 px-3 pr-10 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                       placeholder="0000 0000 0000 0000"
                                       data-stripe="number">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v1h2a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V7a2 2 0 012-2h2V4zm2 1v1h8V5H6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            @error('card_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="expiry" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                <input type="text" id="expiry" name="expiry" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                       placeholder="MM/YY"
                                       data-stripe="expiry">
                                @error('expiry')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cvc" class="block text-sm font-medium text-gray-700">CVC</label>
                                <input type="text" id="cvc" name="cvc" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                       placeholder="123"
                                       data-stripe="cvc">
                                @error('cvc')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center">
                                <input id="save_card" name="save_card" type="checkbox" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="save_card" class="ml-2 block text-sm text-gray-700">
                                    Save card for future purchases
                                </label>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-blue-50 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Your payment information is processed securely. We do not store your credit card details.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="px-6 py-5 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Payment Method</h2>
                    <div class="mt-4 space-y-4">
                        <div class="flex items-center">
                            <input id="credit_card" name="payment_method" type="radio" value="credit_card" 
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                   {{ old('payment_method') == 'credit_card' ? 'checked' : '' }} required>
                            <label for="credit_card" class="ml-3 block text-sm font-medium text-gray-700">Credit Card</label>
                        </div>
                        <div class="flex items-center">
                            <input id="debit_card" name="payment_method" type="radio" value="debit_card" 
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                   {{ old('payment_method') == 'debit_card' ? 'checked' : '' }}>
                            <label for="debit_card" class="ml-3 block text-sm font-medium text-gray-700">Debit Card</label>
                        </div>
                        <div class="flex items-center">
                            <input id="paypal" name="payment_method" type="radio" value="paypal" 
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300"
                                   {{ old('payment_method') == 'paypal' ? 'checked' : '' }}>
                            <label for="paypal" class="ml-3 block text-sm font-medium text-gray-700">PayPal</label>
                        </div>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="px-6 py-5">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('cart.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-500">
                            Return to Cart
                        </a>
                        <div class="space-x-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">We accept:</span>
                                <div class="flex -space-x-2">
                                    <svg class="h-8 w-8" viewBox="0 0 38 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="38" height="24" rx="4" fill="#172B85"/>
                                        <path d="M35 6L25.5 18L22 13.5L16.5 18L8 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5 6L8.5 11L13 6" stroke="#FFB3C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <svg class="h-8 w-8 ml-1" viewBox="0 0 38 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="38" height="24" rx="4" fill="#ED0006"/>
                                        <path d="M23.5 17V7H27L29 9.5L31 7H34.5V17H31.5V10.5L29.5 13H28.5L26.5 10.5V17H23.5Z" fill="white"/>
                                        <path d="M14 17L11.5 9H14.5L16 14.5L17.5 9H20.5L23 17H20L19.5 15H16.5L16 17H14Z" fill="white"/>
                                        <path d="M8 17V9H12C14 9 15 10 15 11.25C15 12.5 14 13.5 12.5 13.5H11V17H8ZM11 11.5V11H12C12.5 11 12.5 11.5 12.5 11.5C12.5 12 12 12 11.5 12H11V11.5Z" fill="white"/>
                                    </svg>
                                </div>
                            </div>
                            <button type="submit" 
                                    class="mt-3 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Pay ${{ number_format($subtotal, 2) }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection