<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class HomeController extends Controller
{
public function index()
{
    $categories = Category::all();

    $products = \App\Models\Product::with('category')
        ->where('is_offer', true)
        ->where(function ($query) {
            $query->whereNull('offer_expires_at')
                  ->orWhere('offer_expires_at', '>', now());
        })
        ->get(); // ✅ FIXED: Fetch the results

    return view('layouts.app', compact('categories', 'products'));
}

}