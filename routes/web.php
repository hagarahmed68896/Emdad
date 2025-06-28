<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\SupplierController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\ProductSuggestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController; // Ensure this is used if you uncomment product.show later
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NotificationController;
use App\Models\Product; // Ensure this is used if you uncomment product.show later
use Illuminate\Http\Request;

// ---
// All your web-facing routes, especially those needing session, should be inside this 'web' group.
Route::middleware('web')->group(function () {

    // Your guest-only routes (login, register) now correctly use the session
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login');

        Route::get('/auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
        Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

        Route::get('/auth/facebook/redirect', [SocialLoginController::class, 'redirectToFacebook'])->name('login.facebook');
        Route::get('/auth/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);
    });

    // These routes also need session management, so they should be inside the 'web' group
    Route::match(['get', 'post'], '/supplier', [SupplierController::class, 'register'])->name('register.supplier');
    Route::match(['get', 'post'], '/register', [RegisterController::class, 'register'])->name('register');
    Route::get('/verify-otp', [OtpController::class, 'showVerificationForm'])->name('otp.verify.show');
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.verify.submit');
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('otp.resend');
    Route::post('/switch-otp-method', [OtpController::class, 'switchOtpMethod'])->name('otp.switch.method');

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

        // Route to handle updating user details
        Route::post('/profile/update-details', [ProfileController::class, 'updateDetails'])->name('profile.updateDetails');

        // Route to handle updating user password
        Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

        Route::post('/profile/update-profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.updateProfilePicture');
        Route::post('/profile/remove-profile-p
        icture', [ProfileController::class, 'removeProfilePicture'])->name('profile.removeProfilePicture');

        Route::post('/products/{product}/toggle-favorite', [FavoriteController::class, 'toggle'])
            ->name('favorites.toggle');

        // Route to display the favorites page
        Route::get('/favorites', [FavoriteController::class, 'index'])
             ->name('favorites.index');
        Route::post('/profile/update-notifications', [ProfileController::class, 'updateNotifications'])->name('profile.updateNotifications');

        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    // Route to display all notifications (e.g., on a dedicated notifications page)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Route to mark all notifications as read (for the button in your popup)
    // Using a POST request is generally better for actions that change data.
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Route to mark a specific notification as read
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    });

    // Public routes that also benefit from session (e.g., for language changes)
    Route::get('/', [HomeController::class, 'index'])->name(name: 'home');
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/products', [CategoryController::class, 'index'])->name('products.index');
    Route::get('/products/category/{slug}', [CategoryController::class, 'filterByCategory'])->name('products.filterByCategory');

    // *** THIS IS THE MISSING ROUTE YOU NEED TO ADD/ENSURE IS PRESENT ***
    Route::get('/categories/{slug}', [CategoryController::class, 'filterByCategory'])->name('categories.show');

    Route::get('/products/suggestions', [ProductSuggestionController::class, 'getSuggestions']);
    // Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show'); // Uncomment if needed for single product view later

    // Route for the main offers page (using ProductController's index)
    Route::get('/offers', [ProductController::class, 'index'])->name('offers.index');

    // Route for individual product details (using ProductController's show with slug)
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

    Route::get('language/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'ar'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }
        return redirect()->back();
    })->name('change.language');

    Route::get('/privacy-policy', function () {
        return view('privacy');
    })->name('privacy');

    Route::get('/terms-conditions', function () {
        return view('terms');
    })->name('terms');

    Route::get('/common_questions', function () {
        return view('common_questions');
    })->name('common_questions');
}); // End of the 'web' middleware group here is the web