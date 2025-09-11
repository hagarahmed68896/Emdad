<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SupplierDashboardController extends Controller
{
    public function index(Request $request)
    {
        $supplierId = Auth::user()->business->id;

        // ✅ Current month sales grouped by week-of-month
        $currentMonthSales = OrderItem::whereHas('product', function ($q) use ($supplierId) {
                $q->where('business_data_id', $supplierId);
            })
            ->whereMonth('created_at', now()->month)
            ->selectRaw('FLOOR((DAY(created_at)-1)/7)+1 as week_of_month, SUM(unit_price * quantity) as total')
            ->groupBy('week_of_month')
            ->pluck('total', 'week_of_month');

        // ✅ Last month sales grouped by week-of-month
        $lastMonthSales = OrderItem::whereHas('product', function ($q) use ($supplierId) {
                $q->where('business_data_id', $supplierId);
            })
            ->whereMonth('created_at', now()->subMonth()->month)
            ->selectRaw('FLOOR((DAY(created_at)-1)/7)+1 as week_of_month, SUM(unit_price * quantity) as total')
            ->groupBy('week_of_month')
            ->pluck('total', 'week_of_month');

        // Fill missing weeks with 0
        $currentMonthSales = collect($currentMonthSales)
            ->union([1 => 0, 2 => 0, 3 => 0, 4 => 0])
            ->sortKeys()
            ->values()
            ->toArray();

        $lastMonthSales = collect($lastMonthSales)
            ->union([1 => 0, 2 => 0, 3 => 0, 4 => 0])
            ->sortKeys()
            ->values()
            ->toArray();

        // Base sales query with filters
        $salesQuery = OrderItem::with(['order', 'product.subCategory.category'])
            ->whereHas('product', fn($q) => $q->where('business_data_id', $supplierId));

        // 🔹 Filters
        if ($request->sort === 'name') {
            $salesQuery->orderBy(Product::select('name')->whereColumn('products.id', 'order_items.product_id'));
        } elseif ($request->sort === 'latest') {
            $salesQuery->orderByDesc('created_at');
        } elseif ($request->sort === 'oldest') {
            $salesQuery->orderBy('created_at', 'asc');
        } else {
            $salesQuery->latest();
        }

        if ($request->filled('category')) {
            $salesQuery->whereHas('product.subCategory.category', fn($q) => $q->where('id', $request->category));
        }

        if ($request->period === 'week') {
            $salesQuery->where('created_at', '>=', Carbon::now()->subWeek());
        } elseif ($request->period === 'month') {
            $salesQuery->where('created_at', '>=', Carbon::now()->subMonth());
        } elseif ($request->period === 'year') {
            $salesQuery->where('created_at', '>=', Carbon::now()->subYear());
        }

        if ($request->filled('price_min')) {
            $salesQuery->whereRaw('(unit_price * quantity) >= ?', [$request->price_min]);
        }
        if ($request->filled('price_max')) {
            $salesQuery->whereRaw('(unit_price * quantity) <= ?', [$request->price_max]);
        }

        $sales = $salesQuery->get();

        $categories = \App\Models\Category::all();

        return view('supplier.dashboard', compact('currentMonthSales', 'lastMonthSales', 'sales', 'categories'));
    }


    /**
     * Return sales data for chart (current & last month)
     */
    public function getSalesChartData()
    {
        $supplierId = Auth::user()->business->id;

        // Current month sales grouped by week-of-month
        $currentMonthSales = OrderItem::whereHas('product', fn($q) => $q->where('business_data_id', $supplierId))
            ->whereMonth('created_at', now()->month)
            ->selectRaw('FLOOR((DAY(created_at)-1)/7)+1 as week_of_month, SUM(unit_price * quantity) as total')
            ->groupBy('week_of_month')
            ->pluck('total', 'week_of_month');

        // Last month sales grouped by week-of-month
        $lastMonthSales = OrderItem::whereHas('product', fn($q) => $q->where('business_data_id', $supplierId))
            ->whereMonth('created_at', now()->subMonth()->month)
            ->selectRaw('FLOOR((DAY(created_at)-1)/7)+1 as week_of_month, SUM(unit_price * quantity) as total')
            ->groupBy('week_of_month')
            ->pluck('total', 'week_of_month');

        // Fill missing weeks with 0
        $currentMonthSales = collect($currentMonthSales)
            ->union([1 => 0, 2 => 0, 3 => 0, 4 => 0])
            ->sortKeys()
            ->values()
            ->toArray();

        $lastMonthSales = collect($lastMonthSales)
            ->union([1 => 0, 2 => 0, 3 => 0, 4 => 0])
            ->sortKeys()
            ->values()
            ->toArray();

        // Return JSON response for chart
        return response()->json([
            'currentMonthSales' => $currentMonthSales,
            'lastMonthSales' => $lastMonthSales
        ]);
    }
}
