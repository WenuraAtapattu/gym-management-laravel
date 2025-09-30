@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                My Orders
            </h2>
            <p class="mt-4 text-xl text-gray-600">
                View your order history and track shipments
            </p>
        </div>

        @if($orders->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <li>
                            <a href="{{ route('orders.show', $order) }}" class="block hover:bg-gray-50">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                    <i class="fas fa-shopping-bag text-orange-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-orange-600">
                                                    Order #{{ $order->id }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $order->created_at->format('M d, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="mr-4 text-right">
                                                <p class="text-sm font-medium text-gray-900">
                                                    ${{ number_format($order->total_amount, 2) }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                                ($order->status === 'shipped' ? 'bg-yellow-100 text-yellow-800' : 
                                                'bg-gray-100 text-gray-800')) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                            <svg class="ml-4 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-bag text-2xl text-orange-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No orders yet</h3>
                <p class="mt-1 text-sm text-gray-500">Start your shopping journey today!</p>
                <div class="mt-6">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Browse Products
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
