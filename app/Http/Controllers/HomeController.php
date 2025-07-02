<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart; // Assuming you have a Cart model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; // For guest cart management

class HomeController extends Controller
{
    /**
     * Display the home page with various product listings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all categories
        $categories = Category::all();

        // Fetch products that are currently on offer
        // Eager load subCategory and its parent Category for display
        $onOfferProducts = Product::with('subCategory.category')
            ->where('is_offer', true)
            ->where(function ($query) {
                // Ensure the offer is active (either no expiry or expiry in the future)
                $query->whereNull('offer_expires_at')
                      ->orWhere('offer_expires_at', '>', now());
            })
            ->get();

        // Fetch products that are marked as featured
        // Eager load subCategory and its parent Category if needed in the view
        $featuredProducts = Product::with('subCategory.category') // Added eager loading here
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc') // Order them by creation date, newest first
            ->limit(8)                     // Limit to 8 products for a grid display
            ->get();

        // Initialize favorites collection
        $favorites = collect();

        // If a user is logged in, fetch their favorite products
        if (Auth::check()) {
            // Eager load product and its nested subCategory and Category for display
            $favorites = Auth::user()->favorites()->with('product.subCategory.category')->get();
        }

        // Initialize cart items and cart object
        $cartItems = collect();
        $cart = null;

        // Determine the current cart based on user login or session
        if (Auth::check()) {
            // For logged-in users, get their associated cart
            $cart = Auth::user()->cart; 
        } else {
            // For guests, try to retrieve the cart using the session ID
            $sessionId = Session::getId();
            $cart = Cart::where('session_id', $sessionId)
                         ->where('status', 'active') // Assuming 'active' status for current cart
                         ->first();
        }

        // If a cart is found, fetch its items and eager load their products
        if ($cart) {
            $cartItems = $cart->items()->with('product')->get();
        }

        // Initialize notifications and unread count
        $notifications = collect();
        $unreadNotificationCount = 0;

        // If a user is logged in, fetch their notifications
        if (Auth::check()) {
            // Fetch the latest 5 notifications for a popup/dropdown
            $notifications = Auth::user()->notifications()->latest()->take(5)->get();
            // Get the total count of unread notifications for a badge
            $unreadNotificationCount = Auth::user()->unreadNotifications->count();
        }

        // Pass all necessary data to the home view
        return view('layouts.app', compact('categories', 'onOfferProducts', 'featuredProducts', 'favorites', 'cartItems', 'notifications', 'unreadNotificationCount'));
    }
}