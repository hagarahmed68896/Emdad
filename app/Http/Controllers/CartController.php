<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection; // Don't forget this import
use App\Models\Product; // Assuming you have a Product model if you link them

class CartController extends Controller
{
    /**
     * Display the shopping cart contents.
     */
    public function index() // <--- THIS IS THE MISSING METHOD
    {

        // Get the cart data from the session
        $cartDataFromSession = session()->get('cart', []);

        // Convert the session cart data into a Laravel Collection
        // And ensure product details are available for Blade
        $cartItems = collect($cartDataFromSession)->map(function ($itemData, $itemIdentifier) {
            // $itemData is the array for each item (e.g., ['product_id' => 1, 'quantity' => 2, ...])

            // Re-fetch product details from the database (safer than storing full model in session)
            $product = Product::find($itemData['product_id']);

            if ($product) {
                // Attach the product model to the item data
                $itemData['product'] = $product;
                return (object) $itemData; // Cast to object for Blade access ($item->product->name)
            } else {
                // Handle case where product is not found (e.g., product deleted)
                // You might return null and filter it out, or log an error.
                return null;
            }
        })->filter()->values(); // Filter out any nulls if products weren't found and re-index

        return view('profile.cart', compact('cartItems'));
    }

    /**
     * Add a product to the shopping cart.
     */
public function store(Request $request)
{
    
    $productId = $request->input('product_id');
    $quantity = $request->input('quantity', 1);
    $options = $request->input('options', []);

    $product = Product::find($productId);

    if (!$product) {
        return back()->with('error', 'Product not found.');
    }

    $cart = session()->get('cart', []);

    $itemIdentifier = $productId;
    if (!empty($options)) {
        ksort($options);
        $itemIdentifier .= '_' . md5(json_encode($options));
    }

    if (isset($cart[$itemIdentifier])) {
        $cart[$itemIdentifier]['quantity'] += $quantity;
    } else {
        $cart[$itemIdentifier] = [
            'product_id'        => $product->id,
            'name'              => $product->name,
            'image'             => $product->image,
            'price_at_addition' => $product->price,
            'quantity'          => $quantity,
            'options'           => $options,
            // 'product' is removed from this example as it's better to re-fetch
        ];
    }

    // TEMPORARY DEBUGGING LINE:
    dd($cart); // <--- Add this line here!

    // Save the updated cart back to the session
    session()->put('cart', $cart);

    if ($request->expectsJson()) {
        return response()->json(['message' => 'Product added to cart!', 'cartCount' => collect($cart)->sum('quantity')]);
    }

    return back()->with('success', 'Product added to cart!');
}

    // You might also add other methods like updateQuantity, removeItem, clearCart here
}