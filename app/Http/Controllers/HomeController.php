<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth; // <-- Don't forget to import Auth
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch all categories (as you already have)
        $categories = Category::all();

        // Fetch products that are currently on offer (as you already have)
        $onOfferProducts = Product::with('category')
            ->where('is_offer', true)
            ->where(function ($query) {
                $query->whereNull('offer_expires_at')
                    ->orWhere('offer_expires_at', '>', now());
            })
            ->get();

        // Fetch products that are marked as featured
        $featuredProducts = Product::where('is_featured', true)
                                   ->orderBy('created_at', 'desc') // Order them as you prefer
                                   ->limit(8)                      // Limit for the display grid
                                   ->get();

        // --- ADD THIS SECTION FOR FAVORITES ---
        $favorites = collect(); // Initialize as an empty collection by default

        if (Auth::check()) {
         
            $favorites = Auth::user()->favorites()->with('product.category')->get();
        }
        // --- END OF ADDED SECTION ---

        // --- Logic for Cart (NEWLY ADDED) ---
        $cartItems = collect(); // Initialize as an empty collection
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
        // --- End Cart Logic ---

        // --- Logic for Notifications (NEWLY ADDED) ---
        $notifications = collect();
        $unreadNotificationCount = 0;
        if (Auth::check()) {
            // Fetch the latest 5 notifications for the popup
            $notifications = Auth::user()->notifications()->latest()->take(5)->get();
            // Get the count of unread notifications for the badge
            $unreadNotificationCount = Auth::user()->unreadNotifications->count();
        }
        // --- End Notifications Logic ---


        // Pass all collections to your 'layouts.app' view
        return view('layouts.app', compact('categories', 'onOfferProducts', 'featuredProducts', 'favorites','cartItems','notifications', 'unreadNotificationCount'));
    }
}