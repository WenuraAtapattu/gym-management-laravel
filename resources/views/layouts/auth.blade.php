<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Steel Muscle-up') }} - {{ $title ?? 'Authentication' }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        
        <style>
            .bg-gym {
                background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset('images/about/pexels-leonardho-1552242.jpg') }}');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }
            .auth-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
            }
        </style>
    </head>
    <body class="bg-gray-100 font-sans antialiased">
        <div class="min-h-screen bg-gym flex flex-col sm:justify-center items-center p-4 sm:p-0">
            <div class="w-full sm:max-w-md px-6 py-8 bg-white auth-card shadow-xl rounded-xl overflow-hidden">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <x-logo />
                </div>

                <!-- Page Title -->
                @isset($header)
                    <h1 class="text-2xl font-bold text-center text-gray-800 mb-8">
                        {{ $header }}
                    </h1>
                @endisset

                <!-- Content -->
                <div class="w-full">
                    @yield('content')
                </div>

                <!-- Footer Links -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
                    @yield('auth-footer')
                    <p class="mt-2">&copy; {{ date('Y') }} {{ config('app.name', 'Steel Muscle-up') }}. All rights reserved.</p>
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
