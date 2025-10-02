@extends('admin.data-explorer.layout')

@section('page-title', 'Shopping Carts Explorer')

@section('explorer-content')
<div class="px-4 py-5 sm:px-6">
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cart ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Items
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Last Updated
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($carts as $cart)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            #{{ $cart->id }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $cart->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cart->user)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="{{ $cart->user->profile_photo_url }}" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $cart->user->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $cart->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500">Guest</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $cart->items->count() }} items</div>
                                        <div class="text-xs text-gray-500">
                                            @foreach($cart->items->take(2) as $item)
                                                {{ $item->product->name }}
                                                @if($item->quantity > 1)
                                                    (x{{ $item->quantity }})
                                                @endif
                                                @if(!$loop->last), @endif
                                            @endforeach
                                            @if($cart->items->count() > 2)
                                                and {{ $cart->items->count() - 2 }} more...
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($cart->items->sum(function($item) {
                                            return $item->quantity * $item->price;
                                        }), 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $cart->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="#" class="text-orange-600 hover:text-orange-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No active carts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $carts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
