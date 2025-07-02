<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's favorited products, handling AJAX for pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            // For AJAX, return a JSON response indicating unauthenticated
            if ($request->ajax()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            // For a regular request, redirect to login
            return redirect()->route('login')->with('error', 'Please log in to view your favorites.');
        }

        $user = Auth::user();

        // Eager load product and its category. Adjust '9' for items per page.
        $favorites = $user->favorites()
                          ->with(['product.category', 'product']) // Ensure product and its category are loaded
                          ->paginate(9); // Crucial for pagination

        // Check if the request is an AJAX request
        if ($request->ajax()) {
            // If AJAX, return only the partial view content
            // The 'partials.favorites_content' contains the grid and pagination links
            return view('partials.favorites_content', compact('favorites'))->render();
        }

        // For a regular, full-page load
        // This will be your main favorites page view, which will *include* the partial
        return view('favorites.index', compact('favorites'));
    }

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

        // Using user()->favorites() relation is cleaner and safer
        $isFavorited = $user->favorites()->where('product_id', $product->id)->exists();

        if ($isFavorited) {
            // If exists, remove from favorites
            $user->favorites()->where('product_id', $product->id)->delete();
            $status = 'removed';
            $message = 'Product removed from favorites.';
        } else {
            // If not, add to favorites
            $user->favorites()->create([
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
}