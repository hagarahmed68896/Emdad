<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle a product in the user's favorites.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = Auth::user();

        // Check if the product is already favorited by the user
        $favorite = Favorite::where('user_id', $user->id)
                            ->where('product_id', $product->id)
                            ->first();

        if ($favorite) {
            // If exists, remove from favorites
            $favorite->delete();
            $status = 'removed';
            $message = 'Product removed from favorites.';
        } else {
            // If not, add to favorites
            Favorite::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            $status = 'added';
            $message = 'Product added to favorites.';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'product_id' => $product->id,
            'is_favorited' => ($status === 'added') // Current state after toggle
        ]);
    }

    /**
     * Display a listing of the user's favorited products.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (!Auth::check()) {
            // Redirect to login or show an error
            return redirect()->route('login')->with('error', 'Please log in to view your favorites.');
        }

        $user = Auth::user();
        // Eager load products for better performance
        $favorites = $user->favorites()->with('product')->get();

        // Optionally, you might want to paginate these
        // $favorites = $user->favorites()->with('product')->paginate(10);

        return view('partials/favorites', compact('favorites'));
    }
}