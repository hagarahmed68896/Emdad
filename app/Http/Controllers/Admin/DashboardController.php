<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Users per week (last 4 weeks)
        $usersPerWeek = User::select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subWeeks(4))
            ->groupBy('week')
            ->pluck('count', 'week');

        // 2. Orders & Revenue per week
        $ordersRevenue = Order::select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue')
            )
            ->where('created_at', '>=', now()->subWeeks(4))
            ->groupBy('week')
            ->get();

        // 3. Products by category
 // In your Admin/DashboardController.php
// 3. Products by category
$productsByCategory = Category::leftJoin('sub_categories', 'categories.id', '=', 'sub_categories.category_id')
    ->leftJoin('products', 'sub_categories.id', '=', 'products.sub_category_id')
    ->select('categories.name as category_name', DB::raw('COUNT(products.id) as product_count'))
    ->groupBy('categories.name')
    ->pluck('product_count', 'category_name'); // This creates a key-value pair array

        // 4. Orders by status
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // 5. Top products
        $topProducts = Product::select('id','name','price','image')
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        // 6. Latest orders
        $latestOrders = Order::latest()->take(5)->get();

        // Metrics
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalSuppliers = User::where('role', 'supplier')->count();
        $totalDocuments = Order::count();

        return view('admin.dashboard', compact(
            'usersPerWeek',
            'ordersRevenue',
            'productsByCategory',
            'ordersByStatus',
            'topProducts',
            'latestOrders',
            'totalUsers',
            'totalCustomers',
            'totalSuppliers',
            'totalDocuments'
        ));
    }
}
