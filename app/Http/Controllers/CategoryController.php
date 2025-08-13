<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


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
      // Find the category based on the slug. If not found, Laravel's firstOrFail will automatically return a 404.
        $selectedCategory = Category::where('slug', $slug)->firstOrFail();

        // Get all categories for navigation or a general category list
        $categories = Category::all();

        // Get subcategories related to the selected category
        $subCategories = SubCategory::where('category_id', $selectedCategory->id)->get();

        // Get products on offer for the selected category
        // This assumes Product has a relationship to SubCategory, and SubCategory to Category.
        $onOfferProducts = Product::whereHas('subCategory.category', function ($query) use ($selectedCategory) {
            $query->where('id', $selectedCategory->id);
        })->where('is_offer', true)->get();

        // Get featured products for the selected category
        $onFeaturedProducts = Product::whereHas('subCategory.category', function ($query) use ($selectedCategory) {
            $query->where('id', $selectedCategory->id);
        })->where('is_featured', true)->get();

        // All categories will now use the same 'categories.show' blade file
        return view('categories.category', compact('categories', 'selectedCategory', 'subCategories', 'onOfferProducts', 'onFeaturedProducts'));
   }

    public function showCategoriesAndProducts()
{
    $categories = Category::with('products')->get(); 

    return view('categories.index', compact('categories'));
}

public function userSubCategoriesWithProducts()
{
    $user = Auth::user();

    if (!$user || !$user->business) {
        return collect(); // no subcategories if user has no business
    }

    $businessId = $user->business->id;

    // Get subcategories where the user's business products exist
    $subCategories = SubCategory::whereHas('products', function ($query) use ($businessId) {
        $query->where('business_data_id', $businessId); // match the business id column in products
    })->with('category') // load parent category
      ->get();

    return view('supplier.supplier_sub_categories', compact('subCategories'));
}


}
