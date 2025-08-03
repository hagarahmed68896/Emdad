<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $onOfferProducts = Product::with('subCategory.category')
            ->where('is_offer', true)
            ->where(function ($query) {
                $query->whereNull('offer_end')
                      ->orWhere('offer_end', '>', now());
            })
            ->get();

        $featuredProducts = Product::with('subCategory.category')
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $favorites = collect();
        $products = collect(); // ✅ Always define it!

        if (Auth::check()) {
            $favorites = Auth::user()->favorites()->with('product.subCategory.category')->get();

            // ✅ ✅ ✅ Add supplier products ONLY for supplier account
            if (Auth::user()->account_type === 'supplier') {
                $products = Product::where('business_data_id', Auth::user()->business_data_id)->get();
            }
        }

        $cartItems = collect();
        $cart = null;

        if (Auth::check()) {
            $cart = Auth::user()->cart;
        } else {
            $sessionId = Session::getId();
            $cart = Cart::where('session_id', $sessionId)
                         ->where('status', 'active')
                         ->first();
        }

        if ($cart) {
            $cartItems = $cart->items()->with('product')->get();
        }

        $notifications = collect();
        $unreadNotificationCount = 0;

        if (Auth::check()) {
            $notifications = Auth::user()->notifications()->latest()->take(5)->get();
            $unreadNotificationCount = Auth::user()->unreadNotifications->count();
        }

        if (Auth::check() && Auth::user()->account_type === 'admin') {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect('/');
        }

        return view('layouts.app', compact(
            'categories',
            'onOfferProducts',
            'featuredProducts',
            'favorites',
            'products', // ✅ Pass it here too!
            'cartItems',
            'notifications',
            'unreadNotificationCount'
        ));
    }
}
