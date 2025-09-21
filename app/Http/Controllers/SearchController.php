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
        $selectedCategories = $request->input('search_categories', []);
        $imageFile = $request->file('search_image');
        $imageUrl = $request->input('image_url');

        $results = collect();

        // 1️⃣ Text Search
        if ($query) {
            if (in_array('products', $selectedCategories) || empty($selectedCategories)) {
                $products = Product::where('name', 'like', "%{$query}%")
                                   ->orWhere('description', 'like', "%{$query}%")
                                   ->get();
                $results = $results->concat($products->map(fn($item) => ['type'=>'product', 'data'=>$item]));
            }
        }

        // 2️⃣ Image Search (upload or URL)
        if ($imageFile || $imageUrl) {
            if ($imageFile) {
                $imagePath = $imageFile->store('public/search_images');
                $results->push([
                    'type'=>'product',
                    'data'=>(object)[
                        'id'=>'img_'.uniqid(),
                        'name'=>'Image Search Result Product',
                        'description'=>'Found via image search',
                        'price'=>99.99,
                        'image'=>Storage::url($imagePath)
                    ]
                ]);
            }
            if ($imageUrl) {
                $results->push([
                    'type'=>'product',
                    'data'=>(object)[
                        'id'=>'url_'.uniqid(),
                        'name'=>'Image URL Search Product',
                        'description'=>'Found via image URL',
                        'price'=>129.99,
                        'image'=>$imageUrl
                    ]
                ]);
            }
        }

        // 3️⃣ Remove duplicates
        $results = $results->unique(fn($item) => $item['type'].'-'.$item['data']->id);

        return view('search.results', [
            'results'=>$results,
            'query'=>$query,
            'selectedCategories'=>$selectedCategories
        ]);
    }
}
