<?php

use App\Http\Controllers\{
    AdminController,
    AdminProductController,
    Auth\ForgotPasswordController,
    Auth\LoginController,
    Auth\RegisterController,
    Auth\ResetPasswordController,
    CartController,
    CheckoutController,
    HomeController,
    OrderController,
    PageController,
    ProductController,
    ProfileController,
    TestMongoController
};
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// MongoDB Test Route
Route::get('/test-mongo', [TestMongoController::class, 'testConnection'])->name('mongo.test');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Show login options page
    Route::view('/login-options', 'auth.login-options')->name('login-options');

    // User login routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // Admin login routes
    Route::prefix('admin')->group(function () {
        Route::get('/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
        Route::post('/login', [LoginController::class, 'adminLogin'])->name('admin.login.submit');
    });

    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
         ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
         ->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
         ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
         ->name('password.update');
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    
    // Account Routes (replaces profile routes)
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'editPassword'])->name('password.edit');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    })->middleware('verified');
    
    // Alias for backward compatibility
    Route::get('/profile', function () {
        return redirect()->route('account.show');
    })->name('profile');
    
    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
    });
    
    // Payments
    Route::get('/payments', \App\Livewire\Admin\Payments\Index::class)->name('payments');

    // Cart Routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/{product}', [CartController::class, 'store'])
             ->where('product', '[0-9]+')
             ->name('store');
        Route::patch('/{cartItem}', [CartController::class, 'update'])
             ->where('cartItem', '[0-9]+')
             ->name('update');
        Route::delete('/{cartItem}', [CartController::class, 'destroy'])
             ->where('cartItem', '[0-9]+')
             ->name('destroy');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    });

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
    });

    // Member Management
    Route::get('/members', \App\Livewire\Admin\Members\Index::class)->name('members.index');
});

// Admin Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminController::class, 'orders'])->name('index');
            Route::get('/{order}', [AdminController::class, 'orderDetails'])->name('show');
            Route::match(['put', 'post'], '/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('status');
        });
        
        // Products
        Route::resource('products', AdminProductController::class)->except(['show']);
        
        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
            Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{user}', [AdminController::class, 'deleteUser'])->name('destroy');
        });
    });

// Redirect /home to root
Route::redirect('/home', '/');