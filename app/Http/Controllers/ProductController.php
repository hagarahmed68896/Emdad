<?php

namespace App\Http\Controllers;

use App\Models\Product; // Make sure to import your Product model!
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display the specified product.
     *
     * @param  string  $slug The slug of the product to display.
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show($slug)
    {
        // Find the product by its 'slug' column.
        // firstOrFail() will automatically throw a 404 error if no product is found,
        // which is good practice for "not found" scenarios.
        $product = Product::where('slug', $slug)->firstOrFail();

        // Pass the fetched product data to a Blade view.
        // You will need to create 'resources/views/products/show.blade.php'
        // to display the details of this product.
        return view('home', compact('product'));
    }

    // You can add other methods here later if needed, e.g., index(), create(), store(), etc.
}