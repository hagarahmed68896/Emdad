<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Import your Product model
use App\Models\Category; // Make sure you also have a Category model if you want to eager load categories
use App\Models\User; // Import the User model
use App\Notifications\NewProductFromSupplier; // Import the notification class

class ProductController extends Controller
{
    /**
     * Display a listing of products, specifically those on offer.
     * This will serve as the main page for "أفضل العروض".
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch products that are currently on offer.
        // We'll use the 'is_offer' column from your migration.
        // We also check 'offer_expires_at' to ensure the offer hasn't expired.
        // `with('category')` eager loads the related category for each product.
        $products = Product::with('category')
                            ->where('is_offer', true) // Filter for products that are marked as an offer
                            ->where(function ($query) {
                                // Ensure the offer has not expired, or offer_expires_at is null (no expiry)
                                $query->whereNull('offer_expires_at')
                                      ->orWhere('offer_expires_at', '>', now());
                            })
                            ->orderBy('created_at', 'desc')
                            ->take(4) // Limiting to 4 products to match the example image's layout
                            ->get();

        // Pass the fetched products to your Blade view.
        // IMPORTANT: The string 'offers' here must exactly match the name of your Blade file (offers.blade.php)
        // and its location (resources/views/offers.blade.php)
        return view('offers', compact('products'));
    }

    /**
     * Display the specified product by its slug.
     * This method is based on the `show` method you provided.
     *
     * @param  string  $slug The slug of the product to display.
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show($slug)
    {
        // Find the product by its 'slug' column.
        // firstOrFail() will automatically throw a 404 error if no product is found.
        // We also eager load the category here, as it's common for detail pages.
        $product = Product::where('slug', $slug)->with('category')->firstOrFail();

        // Pass the fetched product data to a Blade view.
        // This assumes you will have a view file at resources/views/product/show.blade.php
        return view('product.show', compact('product'));
    }

    public function showFeaturedProducts()
    {
        $products = Product::where('is_featured', true)
                           ->orderBy('created_at', 'desc') 
                           ->limit(8)               // Limit to 8 products for the grid layout
                           ->get();

        // Pass the fetched products collection to the Blade view
        return view('products.featured', compact('products'));
    }

     public function store(Request $request)
    {
        // ... (Logic to save the new product) ...
        $product = Product::create($request->all());

        // Assuming you have a supplier relationship on Product or you pass it
        $supplierName = $product->supplier_name ?? 'Unknown Supplier'; // Get supplier name
        $supplierImage = $product->supplier_image_url ?? asset('images/default_supplier.png'); // Get supplier image

        // Find the user(s) to notify
        // Example 1: Notify a specific user (e.g., the admin)
        $adminUser = User::find(1); // Assuming admin user has ID 1
        if ($adminUser) {
            $adminUser->notify(new NewProductFromSupplier($product, $supplierName, $supplierImage));
        }

        // Example 2: Notify all users subscribed to product updates
        // User::where('receives_product_notifications', true)->each(function ($user) use ($product, $supplierName, $supplierImage) {
        //     $user->notify(new NewProductFromSupplier($product, $supplierName, $supplierImage));
        // });

        return redirect()->back()->with('success', 'Product added and notification sent!');
    }
}