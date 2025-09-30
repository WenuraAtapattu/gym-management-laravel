<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">
                {{ __('Manage Reviews') }}
            </h2>
            
            <!-- Stats -->
            <div class="grid grid-cols-1 gap-5 mt-4 sm:grid-cols-3 sm:mt-0">
                <div class="px-4 py-3 bg-white rounded-lg shadow">
                    <div class="text-sm font-medium text-gray-500 truncate">
                        {{ __('Total Reviews') }}
                    </div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ $stats['total'] }}
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-white rounded-lg shadow">
                    <div class="text-sm font-medium text-gray-500 truncate">
                        {{ __('Pending') }}
                    </div>
                    <div class="mt-1 text-2xl font-semibold text-yellow-600">
                        {{ $stats['pending'] }}
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-white rounded-lg shadow">
                    <div class="text-sm font-medium text-gray-500 truncate">
                        {{ __('Approved') }}
                    </div>
                    <div class="mt-1 text-2xl font-semibold text-green-600">
                        {{ $stats['approved'] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex -mb-px space-x-8">
                <button
                    wire:click="changeTab('pending')"
                    @class([
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                        'border-indigo-500 text-indigo-600' => $activeTab === 'pending',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $activeTab !== 'pending'
                    ])
                >
                    {{ __('Pending Reviews') }}
                    @if($stats['pending'] > 0)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $stats['pending'] }}
                        </span>
                    @endif
                </button>
                
                <button
                    wire:click="changeTab('approved')"
                    @class([
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                        'border-indigo-500 text-indigo-600' => $activeTab === 'approved',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $activeTab !== 'approved'
                    ])
                >
                    {{ __('Approved Reviews') }}
                    @if($stats['approved'] > 0)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['approved'] }}
                        </span>
                    @endif
                </button>
            </nav>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6">
            <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="block w-full py-2 pl-10 pr-3 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="{{ __('Search reviews...') }}"
                        >
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <select
                        wire:model.live="perPage"
                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                        <option value="10">10 {{ __('per page') }}</option>
                        <option value="25">25 {{ __('per page') }}</option>
                        <option value="50">50 {{ __('per page') }}</option>
                        <option value="100">100 {{ __('per page') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
            @if($reviews->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($reviews as $review)
                        <li class="p-6 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
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
                                        <h3 class="ml-2 text-lg font-medium text-gray-900">
                                            {{ $review->title }}
                                        </h3>
                                    </div>
                                    
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>{{ $review->comment }}</p>
                                    </div>
                                    
                                    <div class="mt-3 text-sm text-gray-500">
                                        <span>
                                            {{ __('By') }} {{ $review->user ? $review->user->name : __('Guest') }}
                                            @if(isset($review->user) && $review->user->is_guest)
                                                <span class="text-xs text-gray-400">({{ __('Guest') }})</span>
                                            @endif
                                        </span>
                                        <span class="mx-1">•</span>
                                        <span>{{ $review->created_at->diffForHumans() }}</span>
                                        
                                        @if($review->reviewable)
                                            <span class="mx-1">•</span>
                                            <a 
                                                href="{{ route('products.show', $review->reviewable->slug) }}" 
                                                class="text-indigo-600 hover:text-indigo-800 hover:underline"
                                                target="_blank"
                                            >
                                                {{ $review->reviewable->name }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    @if($activeTab === 'pending')
                                        <button
                                            wire:click="approve({{ $review->id }})"
                                            class="p-2 text-green-600 rounded-full hover:bg-green-100"
                                            title="{{ __('Approve') }}"
                                            wire:loading.attr="disabled"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <button
                                        wire:click="confirmReject({{ $review->id }})"
                                        class="p-2 text-red-600 rounded-full hover:bg-red-100"
                                        title="{{ __('Reject') }}"
                                        wire:loading.attr="disabled"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        {{ __('No reviews found') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('There are currently no ' . $activeTab . ' reviews.') }}
                    </p>
                </div>
            @endif
        </div>
        
        <!-- Reject Confirmation Modal -->
        <x-jet-dialog-modal wire:model="showRejectModal">
            <x-slot name="title">
                {{ __('Reject Review') }}
            </x-slot>
            
            <x-slot name="content">
                {{ __('Are you sure you want to reject this review? This action cannot be undone.') }}
            </x-slot>
            
            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$set('showRejectModal', false)" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>
                
                <x-jet-danger-button class="ml-3" wire:click="reject" wire:loading.attr="disabled">
                    {{ __('Reject Review') }}
                </x-jet-danger-button>
            </x-slot>
        </x-jet-dialog-modal>
        
        <!-- Flash Message -->
        <x-jet-action-message class="mt-4" on="saved">
            {{ session('message') }}
        </x-jet-action-message>
    </div>
</div>
