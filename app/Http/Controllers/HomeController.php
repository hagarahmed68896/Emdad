<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Offer;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $onOfferProducts = Product::with('subCategory.category')
    ->whereHas('offer', function ($query) {
        $query->where(function ($q) {
            $q->whereNull('offer_end')
              ->orWhere('offer_end', '>', now());
        });
    })
    ->get();


        $featuredProducts = Product::with('subCategory.category')
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $favorites = collect();
        $products = collect();
        $offers  = collect();

        if (Auth::check()) {
            $favorites = Auth::user()->favorites()->with('product.subCategory.category')->get();
$offers = Auth::user()
    ->offers()
    ->with([
        'product.offer',
        'product.subCategory.category'
    ])
    ->paginate(20); // only load 20 offers at a time

            if (Auth::user()->account_type === 'supplier') {
                $business = Auth::user()->business; // ✅ العلاقة الصحيحة
                // dd(Auth::user()->business);

                if ($business) {
                    $products = $business->products()->get(); // ✅ المنتجات الحقيقية


                }
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

        $supplierCategories = collect();
$supplierCategoryCount = 0;

if (Auth::check() && Auth::user()->account_type === 'supplier') {
    $business = Auth::user()->business;

    if ($business) {
        $supplierCategories = Category::whereHas('subCategories.products', function ($query) use ($business) {
            $query->where('business_data_id', $business->id);
        })->distinct()->get();

        $supplierCategoryCount = $supplierCategories->count();
    }
}

        return view('layouts.app', compact(
            'categories',
            'onOfferProducts',
            'featuredProducts',
            'favorites',
            'products', 
            'cartItems',
            'notifications',
            'unreadNotificationCount',
            'offers',
            'supplierCategories',
            'supplierCategoryCount'
        ));
    }
}
