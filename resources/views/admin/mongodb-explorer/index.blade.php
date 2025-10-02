@extends('layouts.admin')

@section('title', 'MongoDB Explorer')

@push('styles')
<style>
    .database-card {
        @apply bg-white rounded-lg shadow-md p-4 mb-4 hover:shadow-lg transition-shadow duration-200 cursor-pointer;
    }
    .database-card.active {
        @apply border-l-4 border-orange-500;
    }
    .collection-item {
        @apply px-4 py-2 hover:bg-gray-50 cursor-pointer flex justify-between items-center;
    }
    .collection-item.active {
        @apply bg-orange-50 border-r-4 border-orange-500;
    }
    .document-json {
        @apply bg-gray-50 p-4 rounded overflow-auto max-h-96;
    }
    .nav-tab {
        @apply px-4 py-2 text-sm font-medium rounded-t-lg hover:bg-gray-100;
    }
    .nav-tab.active {
        @apply bg-white border-t border-l border-r border-gray-200 text-orange-600;
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    MongoDB Explorer
                </h2>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Database Connection
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Explore and manage your MongoDB data
                </p>
            </div>
            
            <div class="border-t border-gray-200">
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <span class="inline-flex rounded-md shadow-sm">
                        <button type="button" id="refresh-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                            </svg>
                            Refresh
                        </button>
                    </span>
                </div>
                
                <div class="bg-white overflow-hidden">
                    <div class="flex flex-col md:flex-row">
                        <!-- Database List -->
                        <div class="w-full md:w-1/4 border-r border-gray-200">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Databases</h3>
                            </div>
                            <div class="overflow-y-auto" style="max-height: 60vh;">
                                @foreach($databases as $db)
                                    <a href="{{ route('admin.mongodb-explorer.index', ['db' => $db->getName()]) }}" class="block {{ $selectedDb === $db->getName() ? 'bg-gray-50 font-medium' : 'hover:bg-gray-50' }} px-4 py-2 border-b border-gray-100">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
                                                <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
                                                <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />
                                            </svg>
                                            {{ $db->getName() }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        
                        @if($selectedDb)
                        <!-- Collection List -->
                        <div class="w-full md:w-1/4 border-r border-gray-200">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Collections</h3>
                            </div>
                            <div class="overflow-y-auto" style="max-height: 60vh;">
                                @foreach($collections as $collection)
                                    @php
                                        $collectionName = is_object($collection) ? $collection->getName() : $collection['name'];
                                    @endphp
                                    <a href="{{ route('admin.mongodb-explorer.index', ['db' => $selectedDb, 'collection' => $collectionName]) }}" class="block {{ $selectedCollection === $collectionName ? 'bg-orange-50 font-medium border-r-4 border-orange-500' : 'hover:bg-gray-50' }} px-4 py-2 border-b border-gray-100">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385V4.804zM11 4.804A7.968 7.968 0 0114.5 4c1.255 0 2.443.29 3.5.804v10A7.968 7.968 0 0014.5 14c-1.669 0-3.218.51-4.5 1.385V4.804z" />
                                            </svg>
                                            {{ $collectionName }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            
                            @if($stats)
                            <div class="p-4 border-t border-gray-200 bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Database Stats</h4>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <div class="flex justify-between">
                                        <span>Collections:</span>
                                        <span class="font-medium">{{ $stats['collections'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Documents:</span>
                                        <span class="font-medium">{{ number_format($stats['objects'] ?? 0) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Size:</span>
                                        <span class="font-medium">{{ number_format(($stats['dataSize'] ?? 0) / 1024 / 1024, 2) }} MB</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Storage:</span>
                                        <span class="font-medium">{{ number_format(($stats['storageSize'] ?? 0) / 1024 / 1024, 2) }} MB</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Document List -->
                        <div class="w-full md:w-2/4">
                            @if($selectedCollection)
                                <div class="border-b border-gray-200">
                                    <nav class="flex -mb-px">
                                        <a href="#" class="nav-tab active">Documents</a>
                                        <a href="#" class="nav-tab">Query</a>
                                        <a href="#" class="nav-tab">Indexes</a>
                                    </nav>
                                </div>
                                
                                <div class="p-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $selectedCollection }}
                                            <span class="text-sm text-gray-500 font-normal">({{ count($documents) }} documents)</span>
                                        </h3>
                                        <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                            <svg class="-ml-1 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            New Document
                                        </button>
                                    </div>
                                    
                                    @if(count($documents) > 0)
                                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                            <ul class="divide-y divide-gray-200">
                                                @foreach($documents as $document)
                                                    <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                                        <div class="flex items-center justify-between">
                                                            <div class="truncate">
                                                                <div class="flex items-center">
                                                                    <p class="text-sm font-medium text-orange-600 truncate">
                                                                        {{ $document['_id'] }}
                                                                    </p>
                                                                </div>
                                                                <div class="mt-1 text-xs text-gray-500 truncate">
                                                                    {{ json_encode($document) }}
                                                                </div>
                                                            </div>
                                                            <div class="ml-2 flex-shrink-0 flex">
                                                                <button type="button" class="view-document inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500" data-document-id="{{ $document['_id'] }}">
                                                                    View
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <div class="text-center py-12">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">No documents</h3>
                                            <p class="mt-1 text-sm text-gray-500">
                                                Get started by creating a new document.
                                            </p>
                                            <div class="mt-6">
                                                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    New Document
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="p-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No collection selected</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Select a collection to view its documents.
                                    </p>
                                </div>
                            @endif
                        </div>
                        @else
                        <div class="w-full p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H6a2 2 0 00-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No database selected</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Select a database to get started.
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Modal -->
<div id="document-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
            <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                <button type="button" id="close-modal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Document Details
                    </h3>
                    <div class="mt-2">
                        <pre id="document-content" class="document-json"></pre>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" id="close-modal-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
                <button type="button" id="copy-json-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Copy JSON
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Refresh button
        document.getElementById('refresh-btn').addEventListener('click', function() {
            window.location.reload();
        });
        
        // Document modal
        const modal = document.getElementById('document-modal');
        const closeModal = document.getElementById('close-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const copyJsonBtn = document.getElementById('copy-json-btn');
        const documentContent = document.getElementById('document-content');
        
        // View document
        document.querySelectorAll('.view-document').forEach(button => {
            button.addEventListener('click', function() {
                const documentId = this.getAttribute('data-document-id');
                const database = '{{ $selectedDb }}';
                const collection = '{{ $selectedCollection }}';
                
                fetch(`/admin/mongodb-explorer/document/${documentId}?database=${database}&collection=${collection}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            documentContent.textContent = JSON.stringify(data.data, null, 2);
                            hljs.highlightBlock(documentContent);
                            modal.classList.remove('hidden');
                        } else {
                            alert('Error loading document: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error loading document');
                    });
            });
        });
        
        // Close modal
        function closeDocumentModal() {
            modal.classList.add('hidden');
        }
        
        closeModal.addEventListener('click', closeDocumentModal);
        closeModalBtn.addEventListener('click', closeDocumentModal);
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDocumentModal();
            }
        });
        
        // Copy JSON to clipboard
        copyJsonBtn.addEventListener('click', function() {
            const textarea = document.createElement('textarea');
            textarea.value = documentContent.textContent;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            // Show copied message
            const originalText = copyJsonBtn.textContent;
            copyJsonBtn.textContent = 'Copied!';
            copyJsonBtn.classList.remove('bg-white', 'text-gray-700');
            copyJsonBtn.classList.add('bg-green-100', 'text-green-700');
            
            setTimeout(() => {
                copyJsonBtn.textContent = originalText;
                copyJsonBtn.classList.remove('bg-green-100', 'text-green-700');
                copyJsonBtn.classList.add('bg-white', 'text-gray-700');
            }, 2000);
        });
        
        // Initialize syntax highlighting
        if (typeof hljs !== 'undefined') {
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        }
    });
</script>
@endpush
