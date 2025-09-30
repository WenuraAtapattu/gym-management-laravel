<div class="container px-4 py-8 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Member Management</h1>
        <button wire:click="create" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
            <i class="mr-2 fas fa-plus"></i>Add New Member
        </button>
    </div>

    <!-- Search and Filter -->
    <div class="p-4 mb-6 bg-white rounded-lg shadow-md">
        <div class="flex flex-col justify-between items-center space-y-4 md:flex-row md:space-y-0">
            <div class="w-full md:w-1/3">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.debounce.300ms="search" 
                        placeholder="Search members..."
                        class="py-2 pr-4 pl-10 w-full rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <div class="absolute top-2.5 left-3 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                <select wire:model="perPage" class="px-3 py-2 rounded-md border focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="5">5 per page</option>
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="overflow-hidden bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer" wire:click="sortBy('name')">
                            Name
                            @if($sortField === 'name')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="ml-1 text-gray-400 fas fa-sort"></i>
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Contact
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Membership
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($members as $member)
                        @php
                            $latestMembership = $member->memberships->first();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex flex-shrink-0 justify-center items-center w-10 h-10 bg-gray-200 rounded-full">
                                        <i class="text-gray-500 fas fa-user"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $member->phone }}</div>
                                <div class="text-sm text-gray-500">{{ $member->emergency_contact }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($latestMembership)
                                    <div class="text-sm text-gray-900 capitalize">{{ $latestMembership->type }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($latestMembership->start_date)->format('M d, Y') }} - 
                                        {{ \Carbon\Carbon::parse($latestMembership->end_date)->format('M d, Y') }}
                                    </div>
                                @else
                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 text-yellow-800 bg-yellow-100 rounded-full">
                                        No Membership
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($latestMembership)
                                    @php
                                        $statusClasses = [
                                            'active' => 'bg-green-100 text-green-800',
                                            'inactive' => 'bg-red-100 text-red-800',
                                            'on_hold' => 'bg-yellow-100 text-yellow-800',
                                        ][$latestMembership->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                        {{ ucfirst($latestMembership->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <button wire:click="edit({{ $member->id }})" class="mr-3 text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="delete({{ $member->id }})" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this member?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No members found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4">
            {{ $members->links() }}
        </div>
    </div>

    <!-- Add/Edit Member Modal -->
    @if($showModal)
        <div class="overflow-y-auto fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex justify-center items-end px-4 pt-4 pb-20 min-h-screen text-center sm:block sm:p-0">
                <div x-show="showModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="showModal = false"
                     aria-hidden="true"></div>

                <!-- Modal Content -->
                <div x-show="showModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block overflow-hidden px-4 pt-5 pb-4 text-left align-bottom bg-white rounded-lg shadow-xl transition-all transform sm:my-8 sm:w-full sm:max-w-4xl sm:p-6 sm:align-middle">
                    <div class="hidden absolute top-0 right-0 pt-4 pr-4 sm:block">
                        <button type="button" @click="showModal = false" class="text-gray-400 bg-white rounded-md hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 w-full text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                {{ $memberId ? 'Edit Member' : 'Add New Member' }}
                            </h3>
                            <div class="mt-5">
                                <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-6">
                                    <!-- Personal Information -->
                                    <div class="pb-4 border-b border-gray-200 sm:col-span-6">
                                        <h4 class="font-medium text-gray-900 text-md">Personal Information</h4>
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                                        <input type="text" wire:model.lazy="name" id="name" autocomplete="name" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('name') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                        <input type="email" wire:model.lazy="email" id="email" autocomplete="email" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('email') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                                        <input type="text" wire:model.lazy="phone" id="phone" autocomplete="tel" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('phone') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender *</label>
                                        <select id="gender" wire:model.lazy="gender" class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                        @error('gender') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700">Emergency Contact *</label>
                                        <input type="text" wire:model.lazy="emergency_contact" id="emergency_contact" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('emergency_contact') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-6">
                                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                        <textarea id="address" wire:model.lazy="address" rows="2" class="block mt-1 w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                        @error('address') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Membership Information -->
                                    <div class="pt-4 pb-2 border-b border-gray-200 sm:col-span-6">
                                        <h4 class="font-medium text-gray-900 text-md">Membership Information</h4>
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="membership_type" class="block text-sm font-medium text-gray-700">Membership Type *</label>
                                        <select id="membership_type" wire:model.lazy="membership_type" class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                            <option value="basic">Basic</option>
                                            <option value="premium">Premium</option>
                                            <option value="vip">VIP</option>
                                            <option value="student">Student</option>
                                            <option value="senior">Senior</option>
                                            <option value="family">Family</option>
                                        </select>
                                        @error('membership_type') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="price" class="block text-sm font-medium text-gray-700">Price *</label>
                                        <div class="relative mt-1 rounded-md shadow-sm">
                                            <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" wire:model.lazy="price" id="price" class="block pr-12 pl-7 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0.00">
                                        </div>
                                        @error('price') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                                        <input type="date" wire:model.lazy="start_date" id="start_date" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('start_date') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                                        <input type="date" wire:model.lazy="end_date" id="end_date" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('end_date') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                        <select id="status" wire:model.lazy="status" class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                            <option value="active">Active</option>
                                            <option value="expired">Expired</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="on_hold">On Hold</option>
                                        </select>
                                        @error('status') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status *</label>
                                        <select id="payment_status" wire:model.lazy="payment_status" class="block py-2 pr-10 pl-3 mt-1 w-full text-base rounded-md border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                            <option value="paid">Paid</option>
                                            <option value="pending">Pending</option>
                                            <option value="overdue">Overdue</option>
                                            <option value="refunded">Refunded</option>
                                        </select>
                                        @error('payment_status') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 w-full text-base font-medium text-white bg-indigo-600 rounded-md border border-transparent shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ $memberId ? 'Update' : 'Create' }}
                        </button>
                        <button type="button" @click="showModal = false" class="inline-flex justify-center px-4 py-2 mt-3 w-full text-base font-medium text-gray-700 bg-white rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        // Auto-close flash messages after 5 seconds
        document.addEventListener('livewire:load', function () {
            Livewire.on('memberSaved', () => {
                setTimeout(() => {
                    document.querySelectorAll('[x-show*="show"]').forEach(el => {
                        el.style.display = 'none';
                    });
                }, 5000);
            });
        });
    </script>
    @endpush
