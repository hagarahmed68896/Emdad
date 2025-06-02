<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $results = collect(); // Initialize an empty collection

        if ($query) {
            // Example: Search products by name or description
            // $results = Product::where('name', 'like', '%' . $query . '%')
            //                  ->orWhere('description', 'like', '%' . $query . '%')
            //                  ->get();
            // For now, let's just simulate some results
            $results = collect([
                (object)['id' => 1, 'name' => 'Sample Product 1', 'description' => 'Description for product 1'],
                (object)['id' => 2, 'name' => 'Another Product', 'description' => 'Description for another product'],
            ])->filter(function($item) use ($query) {
                return str_contains(strtolower($item->name), strtolower($query)) || str_contains(strtolower($item->description), strtolower($query));
            });
        }

        return view('search.results', compact('query', 'results'));
    }
}
