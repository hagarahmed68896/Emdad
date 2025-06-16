<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Your Product model

class ProductSuggestionController extends Controller
{
    public function getSuggestions(Request $request)
    {
        $query = $request->input('query');
        $suggestions = [];

        if (!empty($query)) {
            // If there's a query, search for relevant products
            $suggestions = Product::where('name', 'like', '%' . $query . '%')
                                 ->orWhere('description', 'like', '%' . $query . '%')
                                 ->limit(10) // Limit search suggestions
                                 ->pluck('name')
                                 ->toArray();
        } else {
            // If query is empty, return default/trending/random products for "Recommended for you"
            // This is what will be shown when the input is focused and empty, or when 'Refresh' is clicked.
            $suggestions = Product::inRandomOrder() // Get random products for variety
                                 ->limit(5) // Default 5 products
                                 ->pluck('name')
                                 ->toArray();
        }

        return response()->json($suggestions);
    }
}