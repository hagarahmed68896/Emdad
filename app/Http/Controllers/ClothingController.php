<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\Category;

class ClothingController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $selectedCategory = Category::where('slug', 'clothing')->firstOrFail();
        $subCategories = SubCategory::where('category_id', $selectedCategory->id)->get();
        $onOfferProducts = $selectedCategory->products()->where('is_offer', true)->get();
        $onFeaturedProducts = $selectedCategory->products()->where('is_featured', true)->get();

        return view('categories.clothing', compact('categories', 'selectedCategory', 'subCategories','onOfferProducts', 'onFeaturedProducts'));
    }
}
