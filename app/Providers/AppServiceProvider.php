<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use App\Models\Cart as CustomCart;
use App\Models\ContactSetting;


class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {

             $view->with('footerSetting', ContactSetting::first());
    
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
