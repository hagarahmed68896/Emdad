<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Cart as CustomCart; // if your Cart model is App\Models\Cart
use Illuminate\Support\Facades\App;
use App\Console\Commands\ChangeAdminPassword;

class AppServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            // --- cartItems logic ---
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

            // --- Other shared variables ---
            $view->with('currentLang', App::getLocale());

            $favorites = Auth::check() ? Auth::user()->favorites()->get() : collect();
            $view->with('favorites', $favorites);

            $notifications = collect();
            $unreadNotificationCount = 0;

            if (Auth::check()) {
                $notifications = Auth::user()->notifications()->latest()->take(5)->get();
                $unreadNotificationCount = Auth::user()->unreadNotifications()->count();
            }

            $view->with('notifications', $notifications);
            $view->with('unreadNotificationCount', $unreadNotificationCount);
        });
    }
}

