@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
                <p class="mt-1 text-sm text-gray-500">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
            </div>
            <a href="{{ route('orders.index') }}" class="text-orange-600 hover:text-orange-700">
                &larr; Back to Orders
            </a>
        </div>

        @if(session('success'))
            <div class="mb-8 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <!-- Order Summary -->
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Order Summary</h3>
            </div>
            <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Order Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                    ($order->status === 'shipped' ? 'bg-yellow-100 text-yellow-800' : 
                                    'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Order Items -->
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
                <div class="mt-6 divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="py-4 flex">
                            <div class="flex-shrink-0 w-24 h-24">
                                @if($item->product && $item->product->image)
                                    <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-center object-cover rounded-md">
                                @else
                                    <div class="w-full h-full bg-gray-200 rounded-md flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-6 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">
                                        @if($item->product)
                                            <a href="{{ route('products.show', $item->product) }}" class="hover:text-orange-600">
                                                {{ $item->product->name }}
                                            </a>
                                        @else
                                            Product Not Available
                                        @endif
                                    </h4>
                                    <p class="ml-4 text-sm font-medium text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Quantity: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping Information -->
            @if($order->shipping)
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Shipping Information</h3>
                    <div class="mt-4">
                        <address class="not-italic">
                            <p class="text-sm text-gray-900">{{ $order->shipping->name }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $order->shipping->address_line1 }}</p>
                            @if($order->shipping->address_line2)
                                <p class="text-sm text-gray-500">{{ $order->shipping->address_line2 }}</p>
                            @endif
                            <p class="text-sm text-gray-500">
                                {{ $order->shipping->city }}, {{ $order->shipping->state }} {{ $order->shipping->postal_code }}
                            </p>
                            @if($order->shipping->country)
                                <p class="text-sm text-gray-500">{{ $order->shipping->country }}</p>
                            @endif
                        </address>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection