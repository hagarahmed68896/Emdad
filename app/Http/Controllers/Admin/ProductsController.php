<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Stats
        $totalProducts = Product::count();
        $availableProducts = Product::where('is_available', true)->count();
        $unavailableProducts = Product::where('is_available', false)->count();
// ✅ Percentages
$availablePercentage = $totalProducts > 0 
    ? round(($availableProducts / $totalProducts) * 100, 2) 
    : 0;

$unavailablePercentage = $totalProducts > 0 
    ? round(($unavailableProducts / $totalProducts) * 100, 2) 
    : 0;
        // ✅ Query with eager loading
        $query = Product::query()
            ->with(['supplier', 'subCategory.category']); 
            // 👈 Loads supplier (BusinessData) + subCategory + parent category

        // ✅ Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // ✅ Availability filter
        if ($request->has('status')) {
            if ($request->status === 'available') {
                $query->where('is_available', true);
            } elseif ($request->status === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        // ✅ Sorting
        switch ($request->input('sort')) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // ✅ Paginate
        $perPage = $request->input('per_page', 10);
        $products = $query->paginate($perPage);

        return view('admin.products.index', [
            'totalProducts' => $totalProducts,
            'availableProducts' => $availableProducts,
            'unavailableProducts' => $unavailableProducts,
            'availablePercentage' => $availablePercentage,
            'unavailablePercentage' => $unavailablePercentage,
            'products' => $products,
            'statusFilter' => $request->input('status'),
            'sortFilter' => $request->input('sort'),
            'search' => $request->input('search'),
        ]);
    }
}
