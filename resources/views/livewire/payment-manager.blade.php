<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Payment Manager</h2>
                    <button wire:click="create" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Record Payment
                    </button>
                </div>

                <!-- Search and Filters -->
                <div class="p-4 mb-6 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700">Search Members</label>
                            <input type="text" id="search" wire:model.debounce.300ms="search" 
                                   class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                   placeholder="Search by name or email...">
                        </div>
                        <div>
                            <label for="dateFrom" class="block text-sm font-medium text-gray-700">From</label>
                            <input type="date" id="dateFrom" wire:model="dateFrom" 
                                   class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="dateTo" class="block text-sm font-medium text-gray-700">To</label>
                            <input type="date" id="dateTo" wire:model="dateTo" 
                                   class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-3">
                        <div>
                            <label for="statusFilter" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="statusFilter" wire:model="statusFilter" class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div>
                            <label for="paymentMethodFilter" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select id="paymentMethodFilter" wire:model="paymentMethodFilter" class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Methods</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-4">
                    <div class="overflow-hidden bg-white rounded-lg shadow">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 p-3 bg-green-500 rounded-md">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 ml-5 w-0">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                ₦{{ number_format($payments->sum('amount'), 2) }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white rounded-lg shadow">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 p-3 bg-blue-500 rounded-md">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 ml-5 w-0">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                {{ $payments->where('status', 'completed')->count() }}
                                            </div>
                                            <div class="flex items-baseline ml-2 text-sm font-semibold text-green-600">
                                                ₦{{ number_format($payments->where('status', 'completed')->sum('amount'), 2) }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white rounded-lg shadow">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 p-3 bg-yellow-500 rounded-md">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 ml-5 w-0">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                {{ $payments->where('status', 'pending')->count() }}
                                            </div>
                                            <div class="flex items-baseline ml-2 text-sm font-semibold text-yellow-600">
                                                ₦{{ number_format($payments->where('status', 'pending')->sum('amount'), 2) }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white rounded-lg shadow">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 p-3 bg-red-500 rounded-md">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div class="flex-1 ml-5 w-0">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Failed/Refunded</dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                {{ $payments->whereIn('status', ['failed', 'refunded'])->count() }}
                                            </div>
                                            <div class="flex items-baseline ml-2 text-sm font-semibold text-red-600">
                                                -₦{{ number_format($payments->whereIn('status', ['failed', 'refunded'])->sum('amount'), 2) }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Member
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Membership
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Method
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10">
                                                <img class="w-10 h-10 rounded-full" src="{{ $payment->member->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($payment->member->name) . '&color=7F9CF5&background=EBF4FF' }}" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $payment->member->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $payment->member->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $payment->membership->type ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $payment->membership ? $payment->membership->start_date->format('M d, Y') : '' }}
                                            @if($payment->membership && $payment->membership->end_date)
                                                - {{ $payment->membership->end_date->format('M d, Y') }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            ₦{{ number_format($payment->amount, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $payment->payment_date->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $payment->payment_date->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">
                                            {{ $payment->payment_method_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {!! $payment->status_badge !!}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        <button wire:click="edit({{ $payment->id }})" class="mr-3 text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button wire:click="delete({{ $payment->id }})" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this payment?')">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-sm text-center text-gray-500">
                                        No payments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Payment Modal -->
    @if($showModal)
        <div class="overflow-y-auto fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex justify-center items-end px-4 pt-4 pb-20 min-h-screen text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$wire.closeModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom bg-white rounded-lg shadow-xl transition-all transform sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            {{ $paymentId ? 'Edit Payment' : 'Record New Payment' }}
                        </h3>
                        
                        <form wire:submit.prevent="store">
                            <div class="space-y-4">
                                <div>
                                    <label for="memberId" class="block text-sm font-medium text-gray-700">Member *</label>
                                    <select id="memberId" wire:model.defer="memberId" 
                                            class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                                            wire:change="$refresh">
                                        <option value="">Select Member</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">
                                                {{ $member->name }} ({{ $member->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('memberId') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="membershipId" class="block text-sm font-medium text-gray-700">Membership *</label>
                                    <select id="membershipId" wire:model.defer="membershipId" 
                                            class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select Membership</option>
                                        @foreach($memberships->where('user_id', $memberId) as $membership)
                                            <option value="{{ $membership->id }}">
                                                {{ $membership->type }} (₦{{ number_format($membership->price, 2) }} - {{ $membership->start_date->format('M d, Y') }} to {{ $membership->end_date->format('M d, Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('membershipId') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount (₦) *</label>
                                        <input type="number" id="amount" wire:model.defer="amount" step="0.01" min="0" 
                                               class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('amount') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="paymentDate" class="block text-sm font-medium text-gray-700">Payment Date *</label>
                                        <input type="date" id="paymentDate" wire:model.defer="paymentDate" 
                                               class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('paymentDate') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="paymentMethod" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                                        <select id="paymentMethod" wire:model.defer="paymentMethod" 
                                                class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                            @foreach($paymentMethods as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('paymentMethod') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                        <select id="status" wire:model.defer="status" 
                                                class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                            @foreach($paymentStatuses as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('status') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea id="notes" wire:model.defer="notes" rows="3" 
                                              class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                    @error('notes') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit" class="inline-flex justify-center px-4 py-2 w-full text-base font-medium text-white bg-indigo-600 rounded-md border border-transparent shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                    {{ $paymentId ? 'Update' : 'Save' }} Payment
                                </button>
                                <button type="button" @click="$wire.closeModal()" class="inline-flex justify-center px-4 py-2 mt-3 w-full text-base font-medium text-gray-700 bg-white rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        // Auto-close flash messages after 5 seconds
        Livewire.on('paymentRecorded', () => {
            setTimeout(() => {
                const alerts = document.querySelectorAll('[x-show*="show"]');
                alerts.forEach(alert => {
                    alert.style.display = 'none';
                });
            }, 5000);
        });
    });
</script>
@endpush
