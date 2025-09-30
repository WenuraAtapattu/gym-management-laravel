<div class="container px-4 py-6 mx-auto">
    <!-- Header -->
    <div class="flex flex-col mb-6 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Order Management</h1>
        <button wire:click="create" class="px-4 py-2 mt-4 text-white bg-blue-600 rounded-md md:mt-0 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Create Order
        </button>
    </div>

    <!-- Filters -->
    <div class="p-4 mb-6 bg-white rounded-lg shadow">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" id="search" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="statusFilter" wire:model.live="statusFilter" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-700">From</label>
                <input type="date" wire:model.live="dateFrom" id="dateFrom" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="dateTo" class="block text-sm font-medium text-gray-700">To</label>
                <input type="date" wire:model.live="dateTo" id="dateTo" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Order #</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Customer</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Amount</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Payment</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">#{{ $order->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $order->user->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                ₦{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ ucfirst($order->payment_status) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <button wire:click="edit({{ $order->id }})" class="mr-3 text-blue-600 hover:text-blue-900">Edit</button>
                                <button wire:click="delete({{ $order->id }})" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this order?')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-sm text-center text-gray-500">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 sm:px-6">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Order Modal -->
    <x-jet-dialog-modal wire:model="showOrderModal">
        <x-slot name="title">
            {{ $orderId ? 'Edit Order' : 'Create New Order' }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-jet-label for="userId" value="Member" />
                    <select id="userId" wire:model="userId" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="userId" class="mt-2" />
                </div>

                <div>
                    <x-jet-label for="status" value="Status" />
                    <select id="status" wire:model="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="status" class="mt-2" />
                </div>

                <div>
                    <x-jet-label for="totalAmount" value="Total Amount" />
                    <div class="relative mt-1 rounded-md shadow-sm">
                        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₦</span>
                        </div>
                        <input type="number" step="0.01" id="totalAmount" wire:model="totalAmount" class="block pr-12 pl-7 w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <x-jet-input-error for="totalAmount" class="mt-2" />
                </div>

                <div>
                    <x-jet-label for="paymentStatus" value="Payment Status" />
                    <select id="paymentStatus" wire:model="paymentStatus" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($paymentStatuses as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="paymentStatus" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('showOrderModal', false)">
                Cancel
            </x-jet-secondary-button>
            <x-jet-button class="ml-2" wire:click="save" wire:loading.attr="disabled">
                Save
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
