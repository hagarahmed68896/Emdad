<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\SupplierController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\ProductSuggestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ClothingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminSupplierController;
use App\Http\Controllers\Admin\BillsController;
use App\Http\Controllers\Admin\DocumentsController;
use App\Http\Controllers\Admin\BannedUserController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ReviewsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Supplier\SupplierProductController;
use App\Models\Product;
use Illuminate\Http\Request;

// ---
// All your web-facing routes, especially those needing session, should be inside this 'web' group.
Route::middleware('web')->group(function () {
     Route::get('/debug-session', function (Illuminate\Http\Request $request) {
          $request->session()->put('foo', 'bar');
          return $request->session()->get('foo');
     });


     // Your guest-only routes (login, register) now correctly use the session
     Route::middleware('guest')->group(function () {
          Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
          Route::post('/login', [LoginController::class, 'login'])->name('login');

          // Admin Login Routes (accessible to guests)
          Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login.show');
          Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.store');

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

          // Admin-specific routes
          Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

               // ✅ لوحة التحكم => فقط احصائيات عامة
               Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                    ->name('admin.dashboard');

               // ✅ إدارة المستخدمين بالكامل
               Route::get('/users', [UserController::class, 'index'])
                    ->name('admin.users.index');

               Route::get('/users/create', [UserController::class, 'create'])
                    ->name('admin.users.create');

               Route::post('/users', [UserController::class, 'store'])
                    ->name('admin.users.store');

               Route::get('/users/{user}/edit', [UserController::class, 'edit'])
                    ->name('admin.users.edit');

               Route::put('/users/{user}', [UserController::class, 'update'])
                    ->name('admin.users.update');

               Route::delete('/users/{user}', [UserController::class, 'destroy'])
                    ->name('admin.users.destroy');
               Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

               Route::get('/users/export/csv', [App\Http\Controllers\Admin\UserController::class, 'exportCsv'])->name('admin.users.export.csv');

               Route::delete('/users/bulk-delete', [App\Http\Controllers\Admin\UserController::class, 'bulkDelete'])->name('admin.users.bulk_delete');

               Route::patch('/admin/users/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('admin.users.toggle-ban');

               Route::get('/suppliers', [AdminSupplierController::class, 'index'])->name('admin.suppliers.index');

               Route::get('/suppliers/create', [AdminSupplierController::class, 'create'])
                    ->name('admin.suppliers.create');
               Route::post('/suppliers/store', [AdminSupplierController::class, 'store'])->name('admin.suppliers.store');

               Route::get('/suppliers/{supplier}/edit', [AdminSupplierController::class, 'edit'])->name('admin.suppliers.edit');

               Route::put('/suppliers/{supplier}', [AdminSupplierController::class, 'update'])->name('admin.suppliers.update');

               Route::get('/suppliers/export/csv', [AdminSupplierController::class, 'exportCsv'])->name('admin.suppliers.export.csv');

               Route::delete('/suppliers/bulk-delete', [AdminSupplierController::class, 'bulkDelete'])->name('admin.suppliers.bulk_delete');

               Route::patch('/admin/suppliers/{supplier}/toggle-ban', [AdminSupplierController::class, 'toggleBan'])->name('admin.suppliers.toggle-ban');

               Route::get('/banned-users', [BannedUserController::class, 'index'])->name('admin.banned.index');

               //account details
               Route::get('/users/{user}', [UserController::class, 'show'])->name('admin.users.show');

               Route::get('/bills/{invoice}/edit', [UserController::class, 'editInvoice'])->name('invoices.edit');

               // عرض PDF للفاتورة
               Route::get('/bills/{id}/show', [UserController::class, 'showPdf'])->name('bills.show_pdf');

               // ✅ إدارة الفواتير بالكامل
               Route::get('/invoices', [BillsController::class, 'index'])->name('invoices.index');

               Route::get('/invoices/create', [BillsController::class, 'create'])->name('invoices.create');

               Route::post('/invoices/store', [BillsController::class, 'store'])->name('invoices.store');

               Route::get('/invoices/{invoice}/edit', [BillsController::class, 'edit'])->name('invoices.edit');
               Route::put('/invoices/{invoice}', [BillsController::class, 'update'])->name('invoices.update');


               Route::delete('/admin/bills/bulk-delete', [BillsController::class, 'bulkDelete'])
                    ->name('admin.bills.bulk_delete');

               Route::get('/bills', [BillsController::class, 'index'])->name('bills.index');
               Route::delete('/bills/bulk-delete', [BillsController::class, 'bulkDelete'])->name('bills.bulk_delete');

               Route::get('/bills/{id}/show', [BillsController::class, 'showPdf'])->name('admin.bills.show_pdf');

               Route::get('/admin/bills/{bill}/download', [BillsController::class, 'downloadPdf'])
                    ->name('admin.bills.download_pdf');

               Route::get('/documents', [DocumentsController::class, 'index'])->name('admin.documents.index');

               Route::get('documents/{id}/edit', [DocumentsController::class, 'edit'])->name('admin.documents.edit');

               Route::put('documents/{id}', [DocumentsController::class, 'update'])->name('admin.documents.update');

               Route::delete('/documents/bulk-delete', [DocumentsController::class, 'bulkDelete'])->name('admin.documents.bulk_delete');

               Route::get('/admin/documents/{id}/download', [DocumentsController::class, 'downloadPdf'])
                    ->name('admin.documents.download');

               Route::delete('documents/{id}', [DocumentsController::class, 'destroy'])
                    ->name('admin.documents.destroy');

               // ✅ إدارة المنتجات بالكامل
               Route::get('/products', [ProductsController::class, 'index'])
                    ->name('admin.products.index');

               Route::get('/products/export/csv', [ProductsController::class, 'exportCsv'])
                    ->name('admin.products.export.csv');

               // ادارة الفئات بالكامل
               Route::get('/categories', [CategoriesController::class, 'index'])
                    ->name('admin.categories.index');

               Route::get('categories/create', [CategoriesController::class, 'create'])->name('admin.categories.create');

               Route::post('categories/store', [CategoriesController::class, 'store'])->name('admin.categories.store');

               Route::delete('/admin/categories/{id}', [CategoriesController::class, 'destroy'])->name('admin.categories.destroy');

               Route::delete('categories/bulk-delete', [CategoriesController::class, 'bulkDelete'])->name('admin.categories.bulkDelete');

               Route::get('/admin/categories/export', [CategoriesController::class, 'exportCsv'])->name('admin.categories.export');

               Route::get('/categories/{id}/edit', [CategoriesController::class, 'edit'])->name('admin.categories.edit');
               Route::put('/categories/{id}', [CategoriesController::class, 'update'])->name('admin.categories.update');

               Route::get('/sub-categories/{id}/edit', [CategoriesController::class, 'editSubCategory'])->name('admin.sub-categories.edit');
               Route::put('/sub-categories/{id}', [CategoriesController::class, 'updateSubCategory'])->name('admin.sub-categories.update');

               // ✅ إدارة الطلبات بالكامل

               Route::get('/orders', [OrdersController::class, 'index'])
                    ->name('admin.orders.index');

               Route::get('/orders/export/csv', [OrdersController::class, 'exportCsv'])
                    ->name('admin.orders.export.csv');

               //ادارة المراجعات بالكامل
               Route::get('/reviews', [ReviewsController::class, 'index'])
                    ->name('admin.reviews.index');

               Route::get('/reviews/export/csv', [ReviewsController::class, 'exportCsv'])
                    ->name('admin.reviews.export.csv');

               Route::delete('reviews/bulk-delete', [ReviewsController::class, 'bulkDelete'])->name('admin.reviews.bulkDelete');

               // ادارة الاعدادات بالكامل
               Route::get('/settings/profile', [SettingsController::class, 'index'])->name('admin.profile.index');
               Route::post('/profile/photo/upload', [SettingsController::class, 'updateProfilePicture'])->name('profile.photo.upload');
               Route::post('/profile/photo/delete', [SettingsController::class, 'removeProfilePicture'])->name('profile.photo.delete');

               Route::put('/profile/update', [SettingsController::class, 'updateProfile'])->name('profile.update');
          });


          // Supplier-specific routes
          Route::get('/', [SupplierProductController::class, 'index'])
              ->name('supplier.products.index');

          Route::get('/supplier/products/create', [SupplierProductController::class, 'create'])->name('products.create');
          Route::post('/supplier/products', [SupplierProductController::class, 'store'])->name('products.store');
          Route::get('/products/{product}/edit', [SupplierProductController::class, 'edit'])->name('products.edit');
          Route::delete('/products/{product}', [SupplierProductController::class, 'destroy'])->name('products.destroy');



          // General user logout (if you have a separate logout for normal users)
          Route::post('logout', [LoginController::class, 'logout'])->name('logout');

          Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
          Route::post('/profile/update-details', [ProfileController::class, 'updateDetails'])->name('profile.updateDetails');
          Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
          Route::post('/profile/update-profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.updateProfilePicture');
          Route::post('/profile/remove-profile-picture', [ProfileController::class, 'removeProfilePicture'])->name('profile.removeProfilePicture');

          Route::post('/products/{product}/toggle-favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
          Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
          Route::post('/profile/update-notifications', [ProfileController::class, 'updateNotifications'])->name('profile.updateNotifications');

          Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
          Route::post('/cart', [CartController::class, 'store'])->name('cart.store');

          Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
          Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
          Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

          Route::post('/reviews/{review}/like', [ReviewController::class, 'toggleLike'])->name('reviews.like');
          Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
          Route::resource('reviews', ReviewController::class)->only(['edit', 'destroy']);
          Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');

          Route::get('/', [HomeController::class, 'index'])->name(name: 'home');
     });

     // Public routes that also benefit from session (e.g., for language changes)
     Route::get('/search', [SearchController::class, 'index'])->name('search');
     Route::get('/products/category/{slug}', [CategoryController::class, 'filterByCategory'])->name('products.filterByCategory');
     Route::get('/products', [ProductController::class, 'index'])->name('products.index');
     Route::get('/offers', [ProductController::class, 'offers'])->name('offers.index');
     Route::get('/products/featured', [ProductController::class, 'showFeaturedProducts'])->name('products.featured');
     Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
     Route::get('/categories/{slug}', [CategoryController::class, 'filterByCategory'])->name('categories.show');
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

     Route::get('/clothings', [ClothingController::class, 'index'])->name('clothings');
});
