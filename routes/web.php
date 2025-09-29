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
use App\Http\Controllers\Supplier\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\supplierrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\supplier\SupplierDashboardController;
use App\Http\Controllers\Admin\FinancialSettlementController;
use App\Http\Controllers\Admin\UserBlocksController;
use App\Http\Controllers\Admin\MessagesController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\ContactSettingController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\Admin\ProfitController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\Admin\QuickReplyController;
use App\Http\Controllers\CameraSearchController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\Admin\AdminAdController;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;





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
Route::get('/admin/password/reset', [AdminLoginController::class, 'showForgotForm'])
    ->name('admin.password.request');

Route::post('/admin/password/email', [AdminLoginController::class, 'sendResetLink'])
    ->name('admin.password.email');

Route::get('/admin/password/reset/{token}', [AdminLoginController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/admin/password/reset', [AdminLoginController::class, 'resetPassword'])
    ->name('admin.password.update');

          Route::get('/auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
          Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

          Route::get('/auth/facebook/redirect', [SocialLoginController::class, 'redirectToFacebook'])->name('login.facebook');
          Route::get('/auth/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);

     //cart
               Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
               Route::get('/cart/last/{product_id}', [CartController::class, 'getLastOrder']);
Route::get('/guest-products', [CartController::class, 'guestProducts'])
    ->name('guest.products');


               Route::delete('/cart/remove-item/{id}', [CartController::class, 'removeItem'])->name('cart.removeItem');
               Route::patch('/cart/{id}/update-variant', [CartController::class, 'updateVariant'])->name('cart.updateVariant');
               Route::delete('/cart/{id}/remove-variant', [CartController::class, 'removeVariant'])->name('cart.removeVariant');
               Route::delete('/cart/bulk-delete', [CartController::class, 'bulkDelete'])->name('cart.bulkRemove');

     });

     // These routes also need session management, so they should be inside the 'web' group
     Route::match(['get', 'post'], '/supplier', [SupplierController::class, 'register'])->name('register.supplier');
     Route::match(['get', 'post'], '/register', [RegisterController::class, 'register'])->name('register');
     // Route::get('/verify-otp', [OtpController::class, 'showVerificationForm'])->name('otp.verify.show');
     // Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.verify.submit');
     // Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('otp.resend');
     // Route::post('/switch-otp-method', [OtpController::class, 'switchOtpMethod'])->name('otp.switch.method');
// routes/web.php or api.php
Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('sendOtp');
Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('verifyOtp');

// Route for downloading product attachments
Route::get('/products/{product}/download-attachment', [ProductController::class, 'downloadAttachment'])->name('products.download.attachment');
     // Authenticated routes
     Route::middleware('auth')->group(function () {

          Route::post('/user/save-location', [RegisterController::class, 'saveLocation'])
    ->name('user.saveLocation');
          
               Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
               Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

               //orders
               Route::get('/order', [OrderController::class, 'index'])->name('order.index');
               Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
               Route::delete('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
               Route::put('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])
                ->name('orders.update-status');

             Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
             Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
             Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
             Route::delete('/conversations/{id}', [ConversationController::class, 'destroy']);
// routes/web.php
// Only use one of these, not both. This is the better, more RESTful option.
Route::post('/users/{user}/report', [ReportController::class, 'reportUser'])->name('reports.user');


Route::post('/suppliers/{user}/toggle-ban', [MessageController::class, 'toggleBan'])->name('suppliers.toggleBan');
Route::post('/suppliers/{user}/ban', [MessageController::class, 'ban'])->name('suppliers.ban');
Route::post('/suppliers/{user}/unban', [MessageController::class, 'unban'])->name('suppliers.unban');
Route::post('/conversations/{id}/mark-read', [MessageController::class, 'markAsRead']);
// Route for a POST request to toggle the block status of a user
Route::post('/users/{user}/toggle-block', [MessageController::class, 'toggleBlock']);

             Route::post('/messages/upload-attachment', [MessageController::class,'uploadAttachment'])->name('messages.upload-attachment');
             
        Route::get('/orders/{order}/products', [ReviewController::class, 'productsByOrder'])
         ->name('orders.products');

         Route::get('/suppliers/{id}', [SupplierrController::class, 'show'])->name('suppliers.show');

Route::get('supplier/ads', [AdController::class, 'index'])->name('supplier.ads.index');
Route::get('supplier/ads/create', [AdController::class, 'create'])->name('supplier.ads.create');
Route::post('supplier/ads', [AdController::class, 'store'])->name('supplier.ads.store');
    Route::get('/ads/{ad}/edit', [AdController::class, 'edit'])->name('supplier.ads.edit');
    
    // Update request
    Route::put('supplier/ads/{ad}', [AdController::class, 'update'])->name('supplier.ads.update');

    // Delete ad
    Route::delete('supplier/ads/{ad}', [AdController::class, 'destroy'])->name('supplier.ads.destroy');
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

               // Route::get('/banned-users', [BannedUserController::class, 'index'])->name('admin.banned.index');

               Route::get('/banned-users', [UserBlocksController::class, 'index'])->name('admin.banned.index');
    Route::post('/banned-users/{id}/ban', [UserBlocksController::class, 'ban'])->name('banned.ban');
    Route::delete('/banned-users/{id}/unban', [UserBlocksController::class, 'unban'])->name('banned.unban');

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

               Route::get('/documents/{document}', [DocumentsController::class, 'showFile'])->name('documents.show');

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
// routes/web.php
Route::post('/reviews/{review}/close', [ReviewController::class, 'close'])->name('admin.reviews.close');
Route::post('/reviews/{review}/action', [ReviewController::class, 'takeAction'])
    ->name('admin.reviews.action');

               // ادارة الاعدادات بالكامل
               Route::get('/settings/profile', [SettingsController::class, 'index'])->name('admin.profile.index');
               Route::post('/profile/photo/upload', [SettingsController::class, 'updateProfilePicture'])->name('profile.photo.upload');
               Route::delete('/profile/photo/delete', [SettingsController::class, 'removeProfilePicture'])->name('profile.photo.delete');

               Route::put('/profile/update', [SettingsController::class, 'updateProfile'])->name('profile.update');
          
               Route::get('settlements', [FinancialSettlementController::class, 'index'])->name('settlements.index');
               Route::get('settlements/create', [FinancialSettlementController::class, 'create'])->name('settlements.create');
               Route::post('settlements', [FinancialSettlementController::class, 'store'])->name('settlements.store');
               Route::get('settlements/{settlement}/edit', [FinancialSettlementController::class, 'edit'])->name('settlements.edit');
               Route::put('settlements/{settlement}', [FinancialSettlementController::class, 'update'])->name('settlements.update');
               Route::delete('settlements/{settlement}', [FinancialSettlementController::class, 'destroy'])->name('settlements.destroy');    
               Route::get('settlements-download', [FinancialSettlementController::class, 'download'])
                                   ->name('settlements.download');
               Route::patch('settlements/{settlement}/transfer', [FinancialSettlementController::class, 'transfer'])
               ->name('settlements.transfer');
              Route::post('/settlements/bulk-transfer', [FinancialSettlementController::class, 'bulkTransfer'])->name('settlements.bulkTransfer');

              //messages
                  Route::get('/messages', [MessagesController::class, 'index'])->name('admin.messages.index');
    Route::get('/messages/{conversation}', [MessagesController::class, 'show'])->name('admin.messages.show');
    Route::delete('/messages/{conversation}', [MessagesController::class, 'destroy'])->name('admin.messages.destroy');
    Route::post('/messages/{conversation}/close', [MessagesController::class, 'close'])->name('admin.messages.close');
Route::post('/conversations/{conversation}/action', [MessagesController::class, 'action'])
     ->name('admin.conversations.action');
        Route::patch('/admin/conversations/{id}/update-status', [MessagesController::class, 'updateStatus'])->name('admin.conversations.updateStatus');
     
        //quick replay
          Route::get('quick_replies', [QuickReplyController::class, 'index'])->name('admin.quick_replies.index');
          Route::get('quick_replies/create', [QuickReplyController::class, 'create'])->name('admin.quick_replies.create');
          Route::post('quick_replies', [QuickReplyController::class, 'store'])->name('admin.quick_replies.store');
          Route::get('quick_replies/{quick_reply}', [QuickReplyController::class, 'show'])->name('admin.quick_replies.show');
          Route::get('quick_replies/{quick_reply}/edit', [QuickReplyController::class, 'edit'])->name('admin.quick_replies.edit');
          Route::put('quick_replies/{quick_reply}', [QuickReplyController::class, 'update'])->name('admin.quick_replies.update');
          Route::delete('quick_replies/{quick_reply}', [QuickReplyController::class, 'destroy'])->name('admin.quick_replies.destroy');

        //reports
        Route::get('/reports', [ReportsController::class, 'index'])
       ->name('admin.reports');

    //notifications

// 📌 Index (list notifications)
Route::get('notifications', [AdminNotificationController::class, 'index'])
    ->name('admin.notifications.index');

// 📌 Create form
Route::get('notifications/create', [AdminNotificationController::class, 'create'])
    ->name('admin.notifications.create');

// 📌 Store (save new notification)
Route::post('notifications', [AdminNotificationController::class, 'store'])
    ->name('admin.notifications.store');

// 📌 Edit form
Route::get('notifications/{id}/edit', [AdminNotificationController::class, 'edit'])
    ->name('admin.notifications.edit');

// 📌 Update notification
Route::put('notifications/{id}', [AdminNotificationController::class, 'update'])
    ->name('admin.notifications.update');

     Route::delete('notifications/bulk-delete', [AdminNotificationController::class, 'bulkDelete'])
     ->name('admin.notifications.bulkDelete');


// 📌 Delete notification
Route::delete('notifications/{id}', [AdminNotificationController::class, 'destroy'])
    ->name('admin.notifications.destroy');

   // Add this route to your admin routes
Route::post('notifications/{notification}/toggle-status', [AdminNotificationController::class, 'toggleStatus'])
     ->name('admin.notifications.toggleStatus');


     //contacts 
     Route::get('contact-settings', [ContactSettingController::class, 'index'])->name('admin.contact.settings');
     Route::post('contact-settings', [ContactSettingController::class, 'store'])->name('admin.contact.settings.store');

     //terms
     // index (list all terms)
    Route::get('terms', [TermController::class, 'index'])->name('admin.terms.index');

    // create (form)
    Route::get('terms/create', [TermController::class, 'create'])->name('admin.terms.create');

    // store (save new term)
    Route::post('terms', [TermController::class, 'store'])->name('admin.terms.store');

    // edit (form)
    Route::get('terms/{term}/edit', [TermController::class, 'edit'])->name('admin.terms.edit');

    // update
    Route::put('terms/{term}', [TermController::class, 'update'])->name('admin.terms.update');

    // delete
    Route::delete('terms/{term}', [TermController::class, 'destroy'])->name('admin.terms.destroy');

    // show (view single term/version)
    Route::get('terms/{term}', [TermController::class, 'show'])->name('admin.terms.show');
        
 // صفحة عرض وتعديل نسبة الأرباح
    Route::get('profit', [ProfitController::class, 'index'])->name('admin.profit.index');
    Route::post('profit', [ProfitController::class, 'store'])->name('admin.profit.store');

    //Faq
     // قائمة الأسئلة
    Route::get('faqs', [FaqController::class, 'index'])->name('admin.faqs.index');
    
    // إضافة سؤال جديد
    Route::get('faqs/create', [FaqController::class, 'create'])->name('admin.faqs.create');
    Route::post('faqs', [FaqController::class, 'store'])->name('admin.faqs.store');
    
    // تعديل سؤال
    Route::get('faqs/{faq}/edit', [FaqController::class, 'edit'])->name('admin.faqs.edit');
    Route::put('faqs/{faq}', [FaqController::class, 'update'])->name('admin.faqs.update');
    
    // حذف سؤال
    Route::delete('faqs/{faq}', [FaqController::class, 'destroy'])->name('admin.faqs.destroy');

    // 🔥 Bulk Delete (حذف متعدد)
    Route::delete('faqs', [FaqController::class, 'bulkDestroy'])->name('admin.faqs.bulk-destroy');

    // 🔥 Download (تحميل CSV أو Excel)
    Route::get('faqs-download', [FaqController::class, 'download'])->name('admin.faqs.download');

    // List all texts
Route::get('site_texts', [\App\Http\Controllers\Admin\SiteTextController::class, 'index'])
    ->name('admin.site_texts.index');

// Show the form to create a new text
Route::get('site_texts/create', [\App\Http\Controllers\Admin\SiteTextController::class, 'create'])
    ->name('admin.site_texts.create');

// Store a new text
Route::post('site_texts', [\App\Http\Controllers\Admin\SiteTextController::class, 'store'])
    ->name('admin.site_texts.store');

// Show a single text (usually optional for admin)
Route::get('site_texts/{site_text}', [\App\Http\Controllers\Admin\SiteTextController::class, 'show'])
    ->name('admin.site_texts.show');

// Show the form to edit a text
Route::get('site_texts/{site_text}/edit', [\App\Http\Controllers\Admin\SiteTextController::class, 'edit'])
    ->name('admin.site_texts.edit');

// Update an existing text
Route::put('site_texts/{site_text}', [\App\Http\Controllers\Admin\SiteTextController::class, 'update'])
    ->name('admin.site_texts.update');

// Delete a text
// Route::delete('site_texts/{site_text}', [\App\Http\Controllers\Admin\SiteTextController::class, 'destroy'])
//     ->name('admin.site_texts.destroy');


//ads
    Route::get('ads', [AdminAdController::class, 'index'])->name('admin.ads.index');
    Route::post('ads/{id}/approve', [AdminAdController::class, 'approve'])->name('admin.ads.approve');
    Route::post('ads/{id}/reject', [AdminAdController::class, 'reject'])->name('admin.ads.reject');
});
               Route::get('/cart', [CartController::class, 'index'])->name('cart.index');


          // Supplier-specific routes
          //products
          Route::get('/supplier', [SupplierProductController::class, 'index'])
              ->name('supplier.products.index');

          Route::get('/supplier/products/create', [SupplierProductController::class, 'create'])->name('products.create');
          Route::post('/supplier/products', [SupplierProductController::class, 'store'])->name('products.store');
          Route::get('/products/{product}/edit', [SupplierProductController::class, 'edit'])->name('products.edit');
          Route::delete('/products/{product}', [SupplierProductController::class, 'destroy'])->name('products.destroy');
          Route::patch('/products/{product}', [SupplierProductController::class, 'update'])->name('products.update');
  
            // Page with the upload form
    Route::get('/products/bulk-upload', [SupplierProductController::class, 'bulkUploadPage'])
         ->name('products.bulkUploadPage');

    // Handle the upload submission
    Route::post('/products/bulk-upload', [SupplierProductController::class, 'bulkUpload'])
         ->name('products.bulkUpload');
Route::get('/products/bulk-upload-template', function() {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = [
        'name','price','model_number','sub_category_id','description','min_order_quantity',
        'preparation_days','shipping_days','production_capacity','product_weight','package_dimensions',
        'material_type','available_quantity','sizes','colors','wholesale_from_1','wholesale_to_1','wholesale_price_1',
        'wholesale_from_2','wholesale_to_2','wholesale_price_2','product_status','offer_name','offer_description',
        'discount_percent','offer_start','offer_end'
    ];

    $sheet->fromArray($headers, NULL, 'A1');

    // Optional: add an example row
    $exampleRow = [
        'Red T-Shirt',100,'TSHIRT001',5,'High quality cotton T-shirt',1,2,3,'500 pcs',0.2,'30x20x2 cm','Cotton',100,'S,M,L','[{"name":"Red","image":""},{"name":"Blue","image":""}]',10,50,90,51,100,85,'ready_for_delivery','Summer Sale','10% off selected colors',10,'2025-07-01','2025-07-31'
    ];
    $sheet->fromArray($exampleRow, NULL, 'A2');

    $writer = new Xlsx($spreadsheet);
    $fileName = 'bulk_products_template.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    $writer->save('php://output');
    exit;
});





          //offers
          Route::get('/supplier', [OfferController::class, 'index'])->name('offeres.index'); // Show all offers
          Route::get('/create', [OfferController::class, 'create'])->name('offers.create'); // Create form
          Route::post('/', [OfferController::class, 'store'])->name('offers.store'); // Store new offer
          Route::get('/{offer}/edit', [OfferController::class, 'edit'])->name('offers.edit'); // Edit form
          Route::put('/{offer}', [OfferController::class, 'update'])->name('offers.update'); // Update offer
          Route::delete('/{offer}', [OfferController::class, 'destroy'])->name('offers.destroy'); // Delete offer

    Route::get('/supplier/dashboard', [SupplierDashboardController::class, 'index'])->name('supplier.dashboard');

          // General user logout (if you have a separate logout for normal users)
          Route::post('logout', [LoginController::class, 'logout'])->name('logout');

          Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
          Route::post('/profile/update-details', [ProfileController::class, 'updateDetails'])->name('profile.updateDetails');
          Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
          Route::post('/profile/update-profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.updateProfilePicture');
          Route::post('/profile/remove-profile-picture', [ProfileController::class, 'removeProfilePicture'])->name('profile.removeProfilePicture');
          Route::put('/business/bank', [ProfileController::class, 'updateBankDetails'])->name('business.bank.update');

          Route::post('/products/{product}/toggle-favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
          Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
          Route::post('/profile/update-notifications', [ProfileController::class, 'updateNotifications'])->name('profile.updateNotifications');

    

          Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
          Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
          Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
          Route::get('/notifications/all', [NotificationController::class, 'getAll'])
          ->name('notifications.all');


          Route::post('/reviews/{review}/like', [ReviewController::class, 'toggleLike'])->name('reviews.like');
          Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
          Route::resource('reviews', ReviewController::class)->only(['edit', 'destroy']);
          Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');

          Route::get('/', [HomeController::class, 'index'])->name(name: 'home');
     });

     // Public routes that also benefit from session (e.g., for language changes)
     Route::post('/search', [SearchController::class, 'index'])->name('search');
     Route::post('/camera-search', [CameraSearchController::class, 'search'])->name('camera.search');
     Route::post('/search/image', [SearchController::class, 'searchByImage'])->name('search.image');
     Route::post('/search/image-url', [SearchController::class, 'searchByImageUrl'])->name('search.image.url');

     Route::get('/products/category/{slug}', [CategoryController::class, 'filterByCategory'])->name('products.filterByCategory');
     Route::get('/products', [ProductController::class, 'index'])->name('products.index');
     Route::get('/products/suggestions', function (Request $request) {
    $query = $request->input('query');
    
    if ($query) {
        // Search for products where the name contains the query string
        $suggestions = Product::where('name', 'LIKE', '%' . $query . '%')
                              ->pluck('name')
                              ->take(5) // Limit the number of suggestions
                              ->toArray();
    } else {
        // If the query is empty, return a list of a few random products
        $suggestions = Product::inRandomOrder()
                              ->pluck('name')
                              ->take(5)
                              ->toArray();
    }

    return response()->json($suggestions);
});
     Route::get('/offers', [ProductController::class, 'offers'])->name('offers.index');
     Route::get('/products/featured', [ProductController::class, 'showFeaturedProducts'])->name('products.featured');
     Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
     Route::get('/products/{product}', [ProductController::class, 'show_notify'])->name('product.show');

     Route::get('/categories/{slug}', [CategoryController::class, 'filterByCategory'])->name('categories.show');
     Route::get('/sub_categories/{slug}', [CategoryController::class, 'userSubCategoriesWithProducts'])->name('sub_categories.show');
     // Route::get('/products/suggestions', [ProductSuggestionController::class, 'getSuggestions']);

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

     Route::post('/contact-us', [ContactMessageController::class, 'store'])->name('contact.store');


     Route::get('/clothings', [ClothingController::class, 'index'])->name('clothings');
});
