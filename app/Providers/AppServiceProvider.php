<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use App\Models\Cart as CustomCart;
use App\Models\ContactSetting;
use App\Models\Term;
use App\Models\Faq;
use App\Models\SiteText;
use App\Models\Category;



class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {

             $view->with('footerSetting', ContactSetting::first());
// حدد نوع المستخدم
$userType = Auth::check() ? Auth::user()->account_type : 'customer';

// الشروط والأحكام
$activeTerms = Term::where('is_active', 1)
    ->where('type', 'terms')
    ->where('user_type', $userType)
    ->latest('updated_at')
    ->get();

// سياسات الخصوصية
$activePolicies = Term::where('is_active', 1)
    ->where('type', 'policies')
    ->where('user_type', $userType)
    ->latest('updated_at')
    ->get();
      //faqs
    $faqs = Faq::where('user_type', Auth::check() ? Auth::user()->type : 'customer')
           ->latest()
           ->get();
$lang = app()->getLocale(); // 'ar' or 'en'

$valueColumn = $lang === 'ar' ? 'value_ar' : 'value_en';

$texts = SiteText::pluck($valueColumn, 'key_name');

$categories = Category::all();

$view->with('activeTerms', $activeTerms)
      ->with('faqs', $faqs)
     ->with('activePolicies', $activePolicies)
     ->with('siteTexts', $texts)
     ->with('categories', $categories);

  
   // ✅ Fix for cURL certificate issue on localhost
    $certPath = 'C:\php-8.4.6\extras\ssl\cacert.pem';

    if (file_exists($certPath)) {
        putenv("CURL_CA_BUNDLE=$certPath");
        putenv("SSL_CERT_FILE=$certPath");
    }


            // ------------------------
            // 🛒 CART ITEMS
            // ------------------------
            $cartItems = collect();
            $cart = null;

            if (Auth::check()) {
                $cart = Auth::user()->cart;
            } else {
                $sessionId = Session::getId();
                $cart = CustomCart::where('session_id', $sessionId)
                    ->where('status', 'active')
                    ->first();
            }

            if ($cart) {
                $cartItems = $cart->items()->with('product')->get();
            }
            $view->with('cartItems', $cartItems);

            // ------------------------
            // 🌍 LANGUAGE
            // ------------------------
            $view->with('currentLang', App::getLocale());

            // ------------------------
            // ❤️ FAVORITES
            // ------------------------
            $favorites = Auth::check() ? Auth::user()->favorites()->get() : collect();
            $view->with('favorites', $favorites);

            // ------------------------
            // 🔔 NOTIFICATIONS
            // ------------------------
            $notifications = collect();
            $unreadNotificationCount = 0;

            if (Auth::check()) {
                $notifications = Auth::user()->notifications()->latest()->take(5)->get();
                $unreadNotificationCount = Auth::user()->unreadNotifications->count();
            }
            $view->with('notifications', $notifications);
            $view->with('unreadNotificationCount', $unreadNotificationCount);

            // ------------------------
            // ✅ NEW: HEADER & HERO
            // ------------------------
      $header = 'partials.header';
$hero = 'partials.heroSection';

if (Auth::check() && Auth::user()->account_type === 'supplier') {
    $header = 'supplier.header_supplier';
    $hero = 'supplier.heroSection_supplier';
}

$view->with('header', $header);
$view->with('hero', $hero);

        });
    }
}
