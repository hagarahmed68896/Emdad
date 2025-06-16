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
use App\Models\Product;
use Illuminate\Http\Request;
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
   
    Route::get('/auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

    Route::get('/auth/facebook/redirect', [SocialLoginController::class, 'redirectToFacebook'])->name('login.facebook');
    Route::get('/auth/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);

});
    Route::match(['get', 'post'], '/supplier', [SupplierController::class, 'register'])->name('register.supplier');

    Route::match(['get', 'post'], '/register', [RegisterController::class, 'register'])->name('register');
    Route::get('/verify-otp', [OtpController::class, 'showVerificationForm'])->name('otp.verify.show');
    Route::post('/verify-otp', [OtpController::class,'verifyOtp'])->name('otp.verify.submit');
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('otp.resend');
    Route::post('/switch-otp-method', [OtpController::class, 'switchOtpMethod'])->name('otp.switch.method');

    Route::middleware('auth')->group(function () {
        Route::post('logout',[LoginController::class,'logout'])->name('logout');
    });
Route::get('/', [HomeController::class, 'index'])->name(name: 'home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/products', [CategoryController::class, 'index'])->name('products.index');
Route::get('/products/category/{slug}', [CategoryController::class, 'filterByCategory'])->name('products.filterByCategory');

// routes/api.php (recommended for API endpoints)
Route::get('/products/suggestions', [ProductSuggestionController::class, 'getSuggestions']);

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
