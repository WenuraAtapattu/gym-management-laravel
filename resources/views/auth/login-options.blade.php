@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="flex justify-center items-center px-4 py-12 min-h-screen bg-gray-50 sm:px-6 lg:px-8">
    <div class="space-y-8 w-full max-w-md">
        <div class="text-center">
            <img class="mx-auto w-auto h-16" src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Welcome Back</h2>
            <p class="mt-2 text-sm text-gray-600">Sign in to your account</p>
        </div>

        @if(session('error'))
            <div class="p-4 bg-red-50 rounded border-l-4 border-red-400">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="px-6 py-8 bg-white rounded-lg shadow sm:px-10">
            <div class="space-y-6">
                <!-- Member Login Button -->
                <div>
                    <a href="{{ route('login') }}" class="flex relative justify-center px-4 py-2 w-full text-sm font-medium text-white bg-orange-600 rounded-md border border-transparent group hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <span class="flex absolute inset-y-0 left-0 items-center pl-3">
                            <svg class="w-5 h-5 text-orange-500 group-hover:text-orange-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Continue as Member
                    </a>
                </div>

                <!-- Divider -->
                <div class="relative">
                    <div class="flex absolute inset-0 items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="flex relative justify-center text-sm">
                        <span class="px-2 text-gray-500 bg-white">
                            Or continue with
                        </span>
                    </div>
                </div>

                <!-- Admin Login Button -->
                <div>
                    <a href="{{ route('admin.login') }}" class="flex relative justify-center px-4 py-2 w-full text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 shadow-sm group hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <span class="flex absolute inset-y-0 left-0 items-center pl-3">
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Admin Login
                    </a>
                </div>
            </div>

            <div class="mt-6">
                <div class="relative">
                    <div class="flex absolute inset-0 items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="flex relative justify-center text-sm">
                        <span class="px-2 text-gray-500 bg-white">
                            New to {{ config('app.name') }}?
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('register') }}" class="flex justify-center px-4 py-2 w-full text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Create your account
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
