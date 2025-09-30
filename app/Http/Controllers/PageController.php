<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        $contactInfo = [
            'address' => '123 Galle Road, Colombo 03, Sri Lanka',
            'phone' => '+94 11 234 5678',
            'email' => 'info@steelgym.lk',
            'hours' => "Monday - Friday: 6:00 AM - 10:00 PM\nSaturday - Sunday: 8:00 AM - 8:00 PM",
            'map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.798511757686!2d79.84521567499746!3d6.927687793079789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2593cf65a1e9d%3A0xf0e3a8097653abfc!2sGalle%20Face%20Green!5e0!3m2!1sen!2slk!4v1630000000000!5m2!1sen!2slk" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'
        ];

        return view('pages.contact', compact('contactInfo'));
    }

    public function products()
    {
        $products = Product::where('is_active', true)->get();
        return view('products.index', compact('products'));
    }

    /**
     * Handle contact form submission
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Here you would typically send an email or save the contact form data
        // For now, we'll just redirect back with a success message
        
        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
