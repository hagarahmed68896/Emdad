<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

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
                                   ->limit(8)                     // Limit for the display grid
                                   ->get();

        // Pass all collections to your 'layouts.app' view
        return view('layouts.app', compact('categories', 'onOfferProducts', 'featuredProducts'));
    }

}