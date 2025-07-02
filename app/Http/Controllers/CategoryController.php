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
        // If slug is 'clothing', delegate to ClothingController
        if ($slug === 'clothing') {
            return app(ClothingController::class)->index();
        }

        $selectedCategory = Category::where('slug', $slug)->firstOrFail();
        $categories = Category::all();

        $viewPath = 'categories.' . $slug;

        if (!view()->exists($viewPath)) {
            abort(404, "View for category [$slug] not found.");
        }

        return view($viewPath, compact('categories', 'selectedCategory'));
    }

    public function showCategoriesAndProducts()
{
    $categories = Category::with('products')->get(); 

    return view('categories.index', compact('categories'));
}
}
