<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection; 
use App\Models\Product; 
use App\Models\Cart; 
use App\Models\CartItem; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class CartController extends Controller
{
    /**
     * Display the shopping cart contents.
     */
/**
     * Display the shopping cart contents for the authenticated user.
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // If a user is logged in, fetch their cart from the database
        if ($user) {
            $cart = Cart::where('user_id', $user->id)->first();
            
            // If a cart exists, load its items and the related product details
            if ($cart) {
                $cartItems = $cart->items()->with('product')->get();
            } else {
                // If the user has no cart, return an empty collection
                $cartItems = collect();
            }
        } else {
            // If the user is not authenticated, a cart cannot exist in the database
            $cartItems = collect();
        }

        return view('profile.cart', compact('cartItems'));
    }

    /**
     * Add a product to the shopping cart.
     */
public function store(Request $request)
{
    try {
        if (!Auth::check()) {
            return response()->json([
                'status'  => 'error',
                'message' => __('messages.must_login_before_cart'),
            ], 401);
        }

        $validated = $request->validate([
            'product_id'         => 'required|exists:products,id',
            'items'              => 'required|array|min:1',
            'items.*.color'      => 'required|string|max:255',
            'items.*.size'       => 'nullable|string|max:255',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        // 1. Find or create the user's cart
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($validated['items'] as $item) {
            // Look for an existing cart item with same product
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($cartItem) {
                // get existing options or start fresh
                $options = $cartItem->options ?? ['variants' => []];

                // build a unique key: "red" OR "red|XXL"
                $variantKey = $item['color'] . ($item['size'] ? '|' . $item['size'] : '');

                // increase quantity for this variant
                if (isset($options['variants'][$variantKey])) {
                    $options['variants'][$variantKey] += $item['quantity'];
                } else {
                    $options['variants'][$variantKey] = $item['quantity'];
                }

                // update cart item
                $cartItem->options  = $options;
                $cartItem->quantity = array_sum($options['variants']); // total sum of all variants
                $cartItem->save();
            } else {
                // new product in cart
                $variantKey = $item['color'] . ($item['size'] ? '|' . $item['size'] : '');
                CartItem::create([
                    'cart_id'           => $cart->id,
                    'product_id'        => $validated['product_id'],
                    'quantity'          => $item['quantity'],
                    'price_at_addition' => $item['unit_price'],
                    'options'           => [
                        'variants' => [
                            $variantKey => $item['quantity']
                        ]
                    ],
                ]);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.added_to_cart'),
        ], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'status'  => 'validation_error',
            'message' => __('messages.validation_failed'),
            'errors'  => $e->errors(),
        ], 422);

    } catch (\Throwable $e) {
        \Log::error('Cart store error: ' . $e->getMessage());
        return response()->json([
            'status'  => 'error',
            'message' => __("messages.error_adding_to_cart"),
            'error'   => $e->getMessage()
        ], 500);
    }
}


  /**
     * Get the last order details for a specific product and authenticated user.
     * This method is placed logically within the CartController.
     */
    public function getLastOrder(Request $request, $productId)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $user = Auth::user();

        // Find the user's last cart item for the given product
        // Note: The previous logic of querying `Order` is incorrect for a 'cart' feature.
        // It should be looking at the `Cart` and `CartItem` models.
        $lastCartItem = CartItem::whereHas('cart', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('product_id', $productId)
            ->latest() // Assumes a `created_at` timestamp to get the "last" item
            ->first();

        // If a cart item is found, return its details
        if ($lastCartItem) {
            return response()->json([
                'items' => [
                    [
                        'color'    => $lastCartItem->options['color'] ?? 'Default',
                        'quantity' => $lastCartItem->quantity,
                        'unit_price' => $lastCartItem->price_at_addition,
                    ]
                ]
            ]);
        }

        // If no previous cart item is found, return an empty response
        return response()->json(['items' => []]);
    }

    // You might also add other methods like updateQuantity, removeItem, clearCart here
/**
     * Update the quantity of an item in the shopping cart.
     */
    public function update(Request $request, $cartItemId)
    {
        // Check if the user is authenticated.
        if (!Auth::check()) {
            return response()->json(['message' => __('messages.unauthenticated')], 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $user = Auth::user();

        // Find the cart item, making sure it belongs to the authenticated user's cart.
        $cartItem = CartItem::where('id', $cartItemId)
                            ->whereHas('cart', function($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })
                            ->first();

        if (!$cartItem) {
            return response()->json(['message' => __('messages.item_not_found')], 404);
        }

        // Update the quantity.
        $newQuantity = $request->input('quantity');
        
        // If the quantity is 0, delete the item from the cart.
        if ($newQuantity == 0) {
            $cartItem->delete();
            return response()->json(['message' => __('messages.item_removed'), 'status' => 'success']);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        return response()->json(['message' => __('messages.cart_updated'), 'status' => 'success']);
    }

    /**
     * Remove a single item from the cart.
     */
    public function destroy($cartItemId)
    {
        // Check if the user is authenticated.
        if (!Auth::check()) {
            return response()->json(['message' => __('messages.unauthenticated')], 401);
        }

        $user = Auth::user();

        // Find the cart item, ensuring it belongs to the authenticated user.
        $cartItem = CartItem::where('id', $cartItemId)
                            ->whereHas('cart', function($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })
                            ->first();

        if (!$cartItem) {
            return response()->json(['message' => __('messages.item_not_found')], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => __('messages.item_removed'), 'status' => 'success']);
    }


    /**
     * Clear all items from the user's cart.
     */
    public function clear()
    {
        // Check if the user is authenticated.
        if (!Auth::check()) {
            return response()->json(['message' => __('messages.unauthenticated')], 401);
        }

        $user = Auth::user();

        // Find the user's cart.
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['message' => __('messages.cart_is_empty')], 200);
        }

        // Delete all items associated with this cart.
        CartItem::where('cart_id', $cart->id)->delete();
        
        return response()->json(['message' => __('messages.cart_cleared'), 'status' => 'success']);
    }
    





}