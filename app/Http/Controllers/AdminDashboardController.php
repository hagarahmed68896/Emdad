<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // الأعداد الأساسية
        $totalUsers      = User::count();
        $totalCustomers  = User::where('account_type', 'customer')->count();
        $totalSuppliers  = User::where('account_type', 'supplier')->count();
        $totalDocuments  = Document::count();
        $totalOrders     = Order::count();
        $totalRevenue    = Order::sum('total_amount');

        // المستخدمين حسب الأسابيع (آخر 6 أسابيع)
        $usersPerWeek = User::select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subWeeks(6))
            ->groupBy('week')
            ->pluck('count', 'week');

        // ملخص الطلبات والإيرادات (آخر 6 أسابيع)
        $ordersRevenue = Order::select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', now()->subWeeks(6))
            ->groupBy('week')
            ->get();

        // المنتجات حسب الفئات
        $productsByCategory = Product::select('sub_category_id', DB::raw('COUNT(*) as count'))
            ->groupBy('sub_category_id')
            ->pluck('count', 'sub_category_id');

        // الطلبات حسب الحالة
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // أعلى المنتجات مبيعاً (اعتماداً على عدد العناصر في order_items)
        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get(['id', 'name', 'price', 'image']);

        // أحدث الطلبات
        $latestOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCustomers',
            'totalSuppliers',
            'totalDocuments',
            'totalOrders',
            'totalRevenue',
            'usersPerWeek',
            'ordersRevenue',
            'productsByCategory',
            'ordersByStatus',
            'topProducts',
            'latestOrders'
        ));
    }
}
