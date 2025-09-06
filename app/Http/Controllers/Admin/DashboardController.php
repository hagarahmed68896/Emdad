<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. عدد المستخدمين لكل أسبوع (مثال: آخر 4 أسابيع)
        $usersPerWeek = User::select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subWeeks(4))
            ->groupBy('week')
            ->pluck('count', 'week');

        // 2. ملخص الطلبات والإيرادات
        $ordersRevenue = Order::select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue')
            )
            ->where('created_at', '>=', now()->subWeeks(4))
            ->groupBy('week')
            ->get();

        // 3. المنتجات حسب الفئات
        $productsByCategory = Product::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        // 4. إجمالي الطلبات حسب الحالة
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // 5. أعلى المنتجات مبيعاً
        $topProducts = Product::select('id','name','price','image')
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        // 6. أحدث المعاملات
        $latestOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'usersPerWeek',
            'ordersRevenue',
            'productsByCategory',
            'ordersByStatus',
            'topProducts',
            'latestOrders'
        ));
    }
}
