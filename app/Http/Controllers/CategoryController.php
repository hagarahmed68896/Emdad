<?php

namespace App\Http\Controllers;
use App\Models\Category;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Fetch all categories
        $categories = Category::all();
        
        // Return the view with categories
        return view('categories.index', compact('categories'));
    }

    public function filterByCategory($slug)
    {
        // Fetch the category by slug
        $selectedCategory = Category::where('slug', $slug)->firstOrFail();
        $categories = Category::all();
        
        // Return the view with the category
        return view('categories.index', compact('categories', 'selectedCategory'));
    }

    public function showCategoriesAndProducts()
{
    $categories = Category::with('products')->get(); 

    return view('categories.index', compact('categories'));
}
}
