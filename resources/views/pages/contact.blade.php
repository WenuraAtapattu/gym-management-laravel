@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Contact Us
            </h2>
            <p class="mt-4 text-xl text-gray-600">
                We'd love to hear from you. Get in touch with us today!
            </p>
        </div>

        @if(session('success'))
            <div class="mt-8 max-w-2xl mx-auto bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Information -->
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Our Information</h3>
                
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-orange-100 text-orange-600">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">Address</h4>
                            <p class="mt-1 text-gray-600">{{ $contactInfo['address'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-orange-100 text-orange-600">
                                <i class="fas fa-phone-alt text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">Phone</h4>
                            <p class="mt-1 text-gray-600">{{ $contactInfo['phone'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-orange-100 text-orange-600">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">Email</h4>
                            <p class="mt-1 text-gray-600">{{ $contactInfo['email'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-orange-100 text-orange-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">Business Hours</h4>
                            <p class="mt-1 text-gray-600 whitespace-pre-line">{{ $contactInfo['hours'] }}</p>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Follow Us</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">
                                <i class="fab fa-facebook-f text-2xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">
                                <i class="fab fa-twitter text-2xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">
                                <i class="fab fa-instagram text-2xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-orange-600 transition-colors">
                                <i class="fab fa-youtube text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h3>
                
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                            <input type="number" name="age" id="age" min="1" max="120" value="{{ old('age') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                        <input type="text" name="subject" id="subject" required value="{{ old('subject') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 @error('subject') border-red-500 @enderror">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                        <textarea id="message" name="message" rows="4" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Map -->
        <div class="mt-12 bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Find Us on Map</h3>
            <div class="rounded-lg overflow-hidden">
                {!! $contactInfo['map_embed'] !!}
            </div>
        </div>
    </div>
</div>
@endsection
