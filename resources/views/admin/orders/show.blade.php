@extends('layouts.original')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Order #{{ $order->id }}</h1>
            <p class="text-gray-500 text-sm">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800">
            Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Summary -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Order Items ({{ $order->items->sum('quantity') }})
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    @if($item->product && $item->product->image && file_exists(public_path('storage/' . $item->product->image)))
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="h-20 w-20 rounded-md object-cover">
                                    @else
                                        <div class="h-20 w-20 rounded-md bg-gray-200 flex items-center justify-center">
                                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex justify-between">
                                        <h4 class="text-sm font-medium text-gray-900">
                                            {{ $item->product ? $item->product->name : 'Product Not Available' }}
                                        </h4>
                                        <p class="ml-4 text-sm font-medium text-gray-900">
                                            ${{ number_format($item->price * $item->quantity, 2) }}
                                        </p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                        @if(!$item->product)
                                            <span class="text-red-500 text-xs ml-2">(Product not found)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Status -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Order Status
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        <div class="flex items-center space-x-4">
                            <select id="status" name="status" class="flex-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Customer Information
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Contact</h4>
                        <p class="mt-1 text-sm text-gray-600">{{ $order->user->email }}</p>
                        <p class="text-sm text-gray-600">{{ $order->user->phone ?? 'No phone' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Shipping</h4>
                        <address class="mt-1 text-sm text-gray-600 not-italic">
                            {{ $order->shipping->name ?? $order->user->name }}<br>
                            {{ $order->shipping->address_line1 }}<br>
                            @if($order->shipping->address_line2)
                                {{ $order->shipping->address_line2 }}<br>
                            @endif
                            {{ $order->shipping->city }}, {{ $order->shipping->state }} {{ $order->shipping->postal_code }}<br>
                            {{ $order->shipping->country }}
                        </address>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Order Summary
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Subtotal</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($order->subtotal, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Shipping</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($order->shipping_cost, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Tax</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($order->tax, 2) }}</dd>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Discount</dt>
                                <dd class="text-sm font-medium text-red-600">-${{ number_format($order->discount, 2) }}</dd>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 pt-2 flex justify-between">
                            <dt class="text-base font-medium text-gray-900">Total</dt>
                            <dd class="text-base font-medium text-gray-900">${{ number_format($order->total_amount, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
