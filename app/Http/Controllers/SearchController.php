<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $selectedCategories = $request->input('search_categories', []); // Will be an array: ['products', 'suppliers']
        $imageFile = $request->file('search_image'); // For file upload
        $imageUrl = $request->input('image_url'); // For URL input

        $results = collect(); // Collection to hold combined results

        // 1. Handle Text Search
        if ($query) {
            if (in_array('products', $selectedCategories) || empty($selectedCategories)) {
                $products = Product::where('name', 'like', '%' . $query . '%')
                                    ->orWhere('description', 'like', '%' . $query . '%')
                                    ->get();
                $results = $results->concat($products->map(fn($item) => ['type' => 'product', 'data' => $item]));
            }

            // if (in_array('suppliers', $selectedCategories) || empty($selectedCategories)) {
            //     $suppliers = Supplier::where('name', 'like', '%' . $query . '%')
            //                          ->orWhere('description', 'like', '%' . $query . '%')
            //                          ->orWhere('email', 'like', '%' . $query . '%')
            //                          ->get();
            //     $results = $results->concat($suppliers->map(fn($item) => ['type' => 'supplier', 'data' => $item]));
            // }
        }

        // 2. Handle Image Search (Placeholder for actual image recognition logic)
        // This is where you would integrate with an image search API or service.
        if ($imageFile || $imageUrl) {
      
            // Dummy results for demonstration:
            if ($imageFile) {
                // Store the uploaded image
                $imagePath = $imageFile->store('public/search_images');
                // You might process $imagePath with an AI service here
                // For demo, let's just add a generic "image search product"
                 $results->push(['type' => 'product', 'data' => (object)['name' => 'Image Search Result Product', 'description' => 'Found via image search', 'price' => 99.99, 'image' => Storage::url($imagePath), 'id' => 'img_'.uniqid()]]);
            } elseif ($imageUrl) {
                // Process $imageUrl with an AI service here
                // For demo, let's just add a generic "image search product"
                 $results->push(['type' => 'product', 'data' => (object)['name' => 'Image URL Search Product', 'description' => 'Found via image URL', 'price' => 129.99, 'image' => $imageUrl, 'id' => 'url_'.uniqid()]]);
            }
        }


        // Remove duplicates if a result appears in both text and image search
        // You might need a more robust way to identify duplicates if 'id' isn't unique across types
        $results = $results->unique(function ($item) {
            return $item['type'] . '-' . (isset($item['data']->id) ? $item['data']->id : '');
        });

    // dd($query, $results);


        return view('search.results', [
            'results' => $results,
            'query' => $query,
            'selectedCategories' => $selectedCategories,
        ]);
    }
}
