@extends('layouts.app')

@section('content')
<section class="bg-neutral-100 text-black px-8 py-12">
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-1">
            <div class="mt-10">
                <img src="{{ asset('images/about/pexels-leonardho-1552242.jpg') }}" alt="Gym Interior" class="w-full h-auto rounded-lg shadow-lg">
            </div>

            <div class="mt-10">
                <h1 class="text-4xl font-extrabold mb-6">Steel Muscle-up: Built for the Relentless</h1>

                <p class="text-lg mb-4">At <strong>Steel Muscle-up</strong>, we don't just supply gym equipment—we forge champions. Born from the legacy of GS Sports and powered by George Steuart Health, we deliver elite fitness gear for home warriors and professional athletes alike.</p>
                <p class="text-lg mb-4">Our mission is simple: equip every Sri Lankan with the tools to push harder, lift heavier, and live stronger. From hospital-grade sports medicine units to your personal home gym, we provide the full spectrum of fitness solutions.</p>

                <h2 class="text-2xl font-bold mb-4 mt-8">Our Vision</h2>
                <p class="mb-6">To dominate the fitness industry by inspiring athletes to crush limits and redefine strength. We're here to fuel your transformation—physically and mentally.</p>

                <h2 class="text-2xl font-bold mb-4">Our Mission</h2>
                <p class="mb-6">To lead Sri Lanka's fitness revolution by delivering cutting-edge equipment, expert guidance, and relentless motivation. We don't just sell gear—we build legacies.</p>
            </div>
        </div>

        <div class="mt-16 bg-white p-8 rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-6">What We Offer</h2>
            <ul class="list-disc list-inside space-y-3 text-lg">
                <li><span class="font-semibold">Premium Equipment:</span> From free weights to commercial-grade cardio machines.</li>
                <li><span class="font-semibold">End-to-End Solutions:</span> Design, delivery, and installation for home and commercial gyms.</li>
                <li><span class="font-semibold">Trusted Brands:</span> Partnering with global leaders in fitness technology.</li>
                <li><span class="font-semibold">Expert Support:</span> Guidance from seasoned professionals to optimize your training.</li>
            </ul>
        </div>

        <!-- Feature Boxes -->
        <div class="mt-16 grid md:grid-cols-3 gap-8">
            <!-- Box 1 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-500 hover:scale-105">
                <div class="h-48 overflow-hidden">
                    <img src="https://images.pexels.com/photos/1954524/pexels-photo-1954524.jpeg?auto=compress&cs=tinysrgb&w=600" 
                         alt="Premium Equipment" 
                         class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3">Elite Equipment</h3>
                    <p class="text-gray-600">Cutting-edge fitness equipment used by professionals worldwide, designed to help you achieve peak performance.</p>
                </div>
            </div>

            <!-- Box 2 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-500 hover:scale-105">
                <div class="h-48 overflow-hidden">
                    <img src="https://images.pexels.com/photos/1552249/pexels-photo-1552249.jpeg?auto=compress&cs=tinysrgb&w=600" 
                         alt="Expert Training" 
                         class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3">Expert Guidance</h3>
                    <p class="text-gray-600">Learn from certified trainers who provide personalized workout plans and nutrition advice tailored to your goals.</p>
                </div>
            </div>

            <!-- Box 3 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-500 hover:scale-105">
                <div class="h-48 overflow-hidden">
                    <img src="https://images.pexels.com/photos/4162458/pexels-photo-4162458.jpeg?auto=compress&cs=tinysrgb&w=600" 
                         alt="Community Support" 
                         class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3">Community</h3>
                    <p class="text-gray-600">Join a supportive community of fitness enthusiasts who motivate and inspire each other to reach new heights.</p>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <h2 class="text-3xl font-bold text-center mb-8">Our Locations</h2>
            
            <!-- Map Sections -->
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Colombo Branch -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h4 class="text-xl font-semibold mb-4">Colombo Branch</h4>
                    <p class="mb-4">123 Fitness Street, Colombo 3</p>
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe 
                            class="w-full h-64 rounded shadow"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126753.84752428737!2d80.5611161!3d7.2945432!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae3664b046f3c41%3A0x24e5398e861dc6a5!2sKandy!5e0!3m2!1sen!2slk!4v1680000000000"
                            allowfullscreen 
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

                <!-- Galle Branch -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h4 class="text-xl font-semibold mb-4">Galle Branch</h4>
                    <p class="mb-4">456 Ocean View Road, Galle</p>
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe
                            class="w-full h-64 rounded shadow"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126832.72331001091!2d80.1515048!3d6.0535196!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae1745f9c6dc4f7%3A0x7b40f7c3cdbe1dcb!2sGalle!5e0!3m2!1sen!2slk!4v1680000000001"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
