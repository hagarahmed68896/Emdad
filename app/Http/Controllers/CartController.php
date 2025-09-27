<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;


class CartController extends Controller
{
    /**
     * Display the shopping cart contents for the authenticated user.
     */
    // public function index(Request $request)
    // {
    //     // Get the authenticated user
    //     $user = Auth::user();

    //     // If a user is logged in, fetch their cart from the database
    //     if ($user) {
    //         $cart = Cart::where('user_id', $user->id)->first();

    //         // If a cart exists, load its items and the related product details
    //         if ($cart) {
    //             $cartItems = $cart->items()->with('product')->get();
    //         } else {
    //             // If the user has no cart, return an empty collection
    //             $cartItems = collect();
    //         }
    //     } else {
    //         // If the user is not authenticated, a cart cannot exist in the database
    //         $cartItems = collect();
    //     }
    //        $order = null;

    // if ($request->has('order_id')) {
    //     $order = \App\Models\Order::with('orderItems')->find($request->order_id);
    // }

    //     return view('profile.cart', compact('cartItems','order'));
    // }

    /**
     * Add a product to the shopping cart.
     */
    // public function store(Request $request)
    // {
    //     try {
    //         if (!Auth::check()) {
    //             return response()->json([
    //                 'status'  => 'error',
    //                 'message' => __('messages.must_login_before_cart'),
    //             ], 401);
    //         }

    //         $validated = $request->validate([
    //             'product_id'        => 'required|exists:products,id',
    //             'items'              => 'required|array|min:1',
    //             'items.*.color'      => 'required|string|max:255',
    //             'items.*.size'       => 'nullable|string|max:255',
    //             'items.*.quantity'   => 'required|integer|min:1',
    //             'items.*.unit_price' => 'required|numeric|min:0',
    //         ]);

    //         $user = Auth::user();

    //         // 1. Find or create the user's cart
    //         $cart = Cart::firstOrCreate(['user_id' => $user->id]);

    //         foreach ($validated['items'] as $item) {
    //             // Look for an existing cart item with same product
    //             $cartItem = CartItem::where('cart_id', $cart->id)
    //                 ->where('product_id', $validated['product_id'])
    //                 ->first();

    //             if ($cartItem) {
    //                 // get existing options or start fresh
    //                 $options = $cartItem->options ?? ['variants' => []];

    //                 // build a unique key: "red" OR "red|XXL"
    //                 $variantKey = $item['color'] . ($item['size'] ? '|' . $item['size'] : '');

    //                 // increase quantity for this variant
    //                 if (isset($options['variants'][$variantKey])) {
    //                     $options['variants'][$variantKey] += $item['quantity'];
    //                 } else {
    //                     $options['variants'][$variantKey] = $item['quantity'];
    //                 }

    //                 // update cart item
    //                 $cartItem->options  = $options;
    //                 $cartItem->quantity = array_sum($options['variants']); // total sum of all variants
    //                 $cartItem->save();
    //             } else {
    //                 // new product in cart
    //                 $variantKey = $item['color'] . ($item['size'] ? '|' . $item['size'] : '');
    //                 CartItem::create([
    //                     'cart_id'            => $cart->id,
    //                     'product_id'         => $validated['product_id'],
    //                     'quantity'           => $item['quantity'],
    //                     'price_at_addition'  => $item['unit_price'],
    //                     'options'            => [
    //                         'variants' => [
    //                             $variantKey => $item['quantity']
    //                         ]
    //                     ],
    //                 ]);
    //             }
    //         }

    //         return response()->json([
    //             'status'  => 'success',
    //             'message' => __('messages.added_to_cart'),
    //         ], 201);

    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'status'  => 'validation_error',
    //             'message' => __('messages.validation_failed'),
    //             'errors'  => $e->errors(),
    //         ], 422);

    //     } catch (\Throwable $e) {
    //         \Log::error('Cart store error: ' . $e->getMessage());
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => __("messages.error_adding_to_cart"),
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // In CartController.php

// In App\Http\Controllers\YourController.php

public function index(Request $request)
{
    $user = Auth::user();

    // 🔹 Merge guest cart into DB if user logs in
    if ($user && $request->has('guest_cart')) {
        $guestCart = json_decode($request->input('guest_cart'), true);

        if (is_array($guestCart) && !empty($guestCart)) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            foreach ($guestCart as $item) {
                // Ensure the variant key matches the one saved in the database
                $variantKey = $item['color'] . ($item['size'] ? '|' . $item['size'] : '');

                $cartItem = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($cartItem) {
                    $options = $cartItem->options ?? ['variants' => []];

                    // Safely update or add the variant quantity
                    $options['variants'][$variantKey] = ($options['variants'][$variantKey] ?? 0) + $item['quantity'];

                    $cartItem->options  = $options;
                    // Recalculate total quantity for the CartItem (sum of all variants)
                    $cartItem->quantity = array_sum($options['variants']);
                    $cartItem->save();
                } else {
                    CartItem::create([
                        'cart_id'           => $cart->id,
                        'product_id'        => $item['product_id'],
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
        }
    }

    // 🔹 Build cartItems for view
    if ($user) {
        // Authenticated: use DB cart
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = $cart ? $cart->items()->with('product')->get() : collect();
    } else {
        // Guest: hydrate from localStorage data
        $cartItems = collect();
        if ($request->has('guest_cart')) {
            $guestCart = json_decode($request->input('guest_cart'), true);

            if (is_array($guestCart) && !empty($guestCart)) {
                // Collect product IDs and load all at once
                $productIds = collect($guestCart)->pluck('product_id')->unique()->toArray();
                $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

                $cartItems = collect($guestCart)->map(function ($item) use ($products) {
                    $product = $products->get($item['product_id']);
                    
                    // Use a unique ID based on product and variant for Alpine
                    $syntheticId = $item['product_id'] . '-' . $item['color'] . '-' . $item['size'];

                    return (object) [
                        // We use the synthetic ID for Alpine tracking/selection
                        'id'                => $syntheticId, 
                        'product_id'        => $item['product_id'],
                        'quantity'          => $item['quantity'],
                        'price_at_addition' => $item['unit_price'],
                        // The options structure needs to be consistent
                        'options'           => [
                            'variants' => [
                                $item['color'] . ($item['size'] ? '|' . $item['size'] : '') => $item['quantity']
                            ]
                        ],
                        'product'           => $product, // already eager-loaded
                    ];
                });
            }
        }
    }

    $order = $request->has('order_id')
        ? \App\Models\Order::with('orderItems')->find($request->order_id)
        : null;

    return view('profile.cart', compact('cartItems', 'order'));
}




public function store(Request $request)
{
    try {
        // This check is no longer strictly necessary as the JS handles it,
        // but it's good practice for backend validation.
        if (!Auth::check()) {
            return response()->json([
                'status'  => 'error',
                'message' => __('messages.must_login_before_cart'),
            ], 401);
        }

        $validated = $request->validate([
            'product_id'        => 'required|exists:products,id',
            'items'             => 'required|array|min:1',
            'items.*.color'     => 'required|string|max:255',
            'items.*.size'      => 'nullable|string|max:255',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        // Find or create the user's cart
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($validated['items'] as $item) {
            // Find existing cart item for the same product, color, and size
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $validated['product_id'])
                ->whereJsonContains('options->variants->' . $item['color'] . ($item['size'] ? '|' . $item['size'] : ''), $item['quantity']) // More specific query
                ->first();

            $variantKey = $item['color'] . ($item['size'] ? '|' . $item['size'] : '');

            if ($cartItem) {
                // Get existing options or start fresh
                $options = $cartItem->options ?? ['variants' => []];

                // Increase quantity for this variant
                if (isset($options['variants'][$variantKey])) {
                    $options['variants'][$variantKey] += $item['quantity'];
                } else {
                    $options['variants'][$variantKey] = $item['quantity'];
                }

                // Update cart item
                $cartItem->options  = $options;
                $cartItem->quantity = array_sum($options['variants']); // total sum of all variants
                $cartItem->save();
            } else {
                // New item in cart
                CartItem::create([
                    'cart_id'            => $cart->id,
                    'product_id'         => $validated['product_id'],
                    'quantity'           => $item['quantity'],
                    'price_at_addition'  => $item['unit_price'],
                    'options'            => [
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
    // Initialize response items
    $items = [];

    if (Auth::check()) {
        // Authenticated user: fetch from database
        $user = Auth::user();

        $lastCartItem = CartItem::whereHas('cart', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('product_id', $productId)
            ->latest()
            ->with('product') // eager load product
            ->first();

        if ($lastCartItem) {
            $optionsData = $lastCartItem->options;
            if (isset($optionsData['variants']) && is_array($optionsData['variants'])) {
                $variants = $optionsData['variants'];
                $colors = $lastCartItem->product->colors ?? [];

                foreach ($variants as $key => $quantity) {
                    $parts = explode('|', $key);
                    $colorName = $parts[0] ?? null;
                    $size      = $parts[1] ?? null;

                    $colorData = collect($colors)->firstWhere('name', $colorName);

                    if ($colorData) {
                        $relativePath = ltrim(str_replace('storage/', '', $colorData['image']), '/');
                        $swatchImage  = Storage::url($relativePath);
                    } else {
                        $swatchImage = 'https://placehold.co/64x64/F0F0F0/ADADAD?text=N/A';
                    }

                    $items[] = [
                        'color'       => $colorName,
                        'size'        => $size,
                        'quantity'    => $quantity,
                        'unit_price'  => $lastCartItem->price_at_addition,
                        'swatchImage' => $swatchImage,
                    ];
                }
            }
        }
    } else {
        // Guest user: fetch from request input (guest_cart)
        $guestCart = $request->input('guest_cart', []);
        if (!empty($guestCart)) {
            $lastItem = collect($guestCart)
                ->where('product_id', $productId)
                ->last(); // get last added item

            if ($lastItem) {
                $items[] = [
                    'color'       => $lastItem['color'] ?? null,
                    'size'        => $lastItem['size'] ?? null,
                    'quantity'    => $lastItem['quantity'] ?? 1,
                    'unit_price'  => $lastItem['unit_price'] ?? 0,
                    'swatchImage' => $lastItem['swatchImage'] ?? 'https://placehold.co/64x64/F0F0F0/ADADAD?text=N/A',
                ];
            }
        }
    }

    return response()->json(['items' => $items]);
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




public function removeItem($id)
{
    $item = CartItem::findOrFail($id); // find the cart item
    $item->delete(); // delete it from DB

    return redirect()->back()->with('success', 'Item removed from cart.');
}

public function updateVariant(Request $request, $id)
{
    $request->validate([
        'variantKey' => 'required|string',
        'quantity'   => 'required|integer|min:1',
    ]);

    $user = Auth::user();
    $cartItem = CartItem::where('id', $id)
        ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
        ->firstOrFail();

    $variants = $cartItem->options['variants'] ?? [];

    if (!isset($variants[$request->variantKey])) {
        return back()->with('error', 'Variant not found.');
    }

    // Update quantity for that variant
    $variants[$request->variantKey] = $request->quantity;

    $cartItem->options = ['variants' => $variants];
    $cartItem->quantity = array_sum($variants);
    $cartItem->save();

    return back()->with('success', 'Cart updated.');
}

    /**
     * Remove a specific variant from a cart item.
     */
  public function removeVariant(Request $request, $id)
{
    $request->validate([
        'variantKey' => 'required|string',
    ]);

    $user = Auth::user();
    $cartItem = CartItem::where('id', $id)
        ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
        ->firstOrFail();

    $variants = $cartItem->options['variants'] ?? [];

    if (isset($variants[$request->variantKey])) {
        unset($variants[$request->variantKey]);
    }

    if (count($variants) > 0) {
        $cartItem->options = ['variants' => $variants];
        $cartItem->quantity = array_sum($variants);
        $cartItem->save();
    } else {
        $cartItem->delete();
    }

    return back()->with('success', 'Variant removed.');
}

    /**
     * Remove a single item from the cart.
     */
public function destroy($cartItemId)
{
    $user = Auth::user();

    $cartItem = CartItem::where('id', $cartItemId)
        ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
        ->firstOrFail();

    $cartItem->delete();

    return response()->json([
        'success' => true,
        'message' => __('messages.item_removed')
    ]);
}


// In CartController.php

// In CartController.php
public function bulkDelete(Request $request)
{
    $ids = $request->input('ids', []); 
    
    // The correct way to delete multiple items by their IDs
    CartItem::destroy($ids);

    return redirect()->back()->with('success', __('messages.items_deleted'));
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

 public function guestProducts(Request $request)
{
    $ids = explode(',', $request->query('ids', ''));

    $products = Product::whereIn('id', $ids)->get();

    return response()->json(
        $products->map(function ($product) {
            return [
                'id'    => $product->id,
                'name'  => $product->name,
                'image' => $product->image
                    ? asset('storage/' . $product->image) // 👈 force correct URL
                    : 'https://via.placeholder.com/80x80?text=No+Image'
            ];
        })
    );
}



}