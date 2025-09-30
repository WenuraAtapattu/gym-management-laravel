@extends('layouts.original')

@section('content')
<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                About STEEL GYM
            </h2>
            <p class="mt-4 text-xl text-gray-600">
                Your ultimate fitness destination since 2010
            </p>
        </div>

        <div class="mt-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Story</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Founded in 2010, STEEL GYM has grown from a small local gym to one of the most respected fitness centers in the region. 
                        Our mission is to provide top-notch fitness facilities and personalized training to help our members achieve their fitness goals.
                    </p>
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Why Choose Us?</h4>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                State-of-the-art equipment
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Certified personal trainers
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                24/7 access for members
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Group fitness classes
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="rounded-lg overflow-hidden shadow-lg">
                    <img src="{{ asset('images/about/pexels-leonardho-1552242.jpg') }}" alt="Gym Interior" class="w-full h-auto">
                </div>
            </div>
        </div>

        <div class="mt-16">
            <h3 class="text-2xl font-bold text-center text-gray-900 mb-8">Our Trainers</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Trainer 1 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">Trainer 1</span>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-gray-900">John Smith</h4>
                        <p class="text-blue-600 mb-2">Fitness Coach</p>
                        <p class="text-gray-600">10+ years of experience in strength and conditioning</p>
                    </div>
                </div>
                <!-- Trainer 2 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">Trainer 2</span>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-gray-900">Sarah Johnson</h4>
                        <p class="text-blue-600 mb-2">Yoga Instructor</p>
                        <p class="text-gray-600">Certified yoga instructor with 8 years of experience</p>
                    </div>
                </div>
                <!-- Trainer 3 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">Trainer 3</span>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-gray-900">Mike Davis</h4>
                        <p class="text-blue-600 mb-2">CrossFit Coach</p>
                        <p class="text-gray-600">Competitive athlete and certified CrossFit trainer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
