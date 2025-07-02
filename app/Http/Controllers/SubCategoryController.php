<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    public function showClothingSubCategories()
{
    // Fetch sub-categories with category_id = 4
    $subCategories = SubCategory::where('category_id', 4)->get();

    return view('categories.clothing', compact('subCategories'));
}
}
