<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\SupplierController;
use App\Http\Controllers\OtpController;
use App\Models\Product;
use Illuminate\Http\Request;
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::match(['get', 'post'], '/supplier', [SupplierController::class, 'register'])->name('register.supplier');
});
    Route::match(['get', 'post'], '/register', [RegisterController::class, 'register'])->name('register');
    Route::get('/verify-otp', [OtpController::class, 'showVerificationForm'])->name('otp.verify.show');
    Route::post('/verify-otp', [OtpController::class,'verifyOtp'])->name('otp.verify.submit');
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('otp.resend');
    Route::post('/switch-otp-method', [OtpController::class, 'switchOtpMethod'])->name('otp.switch.method');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/products', [CategoryController::class, 'index'])->name('products.index');
Route::get('/products/category/{slug}', [CategoryController::class, 'filterByCategory'])->name('products.filterByCategory');
Route::get('/products/suggestions', function (Request $request) {
    $query = $request->input('query');
    $suggestions = [];
    if ($query) {
        $suggestions = Product::where('name', 'like', '%' . $query . '%')
                              ->select('name')
                              ->distinct()
                              ->limit(10)
                              ->pluck('name')
                              ->toArray();
    }
    return response()->json($suggestions);
});
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
