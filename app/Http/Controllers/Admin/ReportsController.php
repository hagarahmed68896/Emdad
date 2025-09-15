<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // الشهر المختار أو الحالي
        $month = $request->input('month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $month);

        // نطاق الشهر
        $startOfMonth = Carbon::create($year, $monthNumber, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $monthNumber, 1)->endOfMonth();

        // 🔹 Weekly reviews
        $reviews = Review::select(
                DB::raw("WEEK(review_date) - WEEK('{$startOfMonth->format('Y-m-d')}') + 1 as week_of_month"),
                DB::raw("SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as positive"),
                DB::raw("SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as neutral"),
                DB::raw("SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as negative")
            )
            ->whereBetween('review_date', [$startOfMonth, $endOfMonth])
            ->groupBy('week_of_month')
            ->orderBy('week_of_month')
            ->get();

        // 🔹 Weekly revenue
        $revenue = Order::select(
                DB::raw("WEEK(created_at) - WEEK('{$startOfMonth->format('Y-m-d')}') + 1 as week_of_month"),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('week_of_month')
            ->orderBy('week_of_month')
            ->get();

        // 🔹 Products count by category
        $productsByCategory = DB::table('products')
            ->join('sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')
            ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(products.id) as count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // 🔹 Sales by category
        $salesByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')
            ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // 🔹 Sales by subcategory
        $salesBySubCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                'sub_categories.id as sub_category_id',
                'sub_categories.name as sub_category_name',
                'sub_categories.category_id',
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total')
            )
            ->groupBy('sub_categories.id', 'sub_categories.name', 'sub_categories.category_id')
            ->get();

        // 🔹 Orders by status
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('status')
            ->get();

        // 🔹 Activity (customers vs suppliers per week)
        $activity = User::select(
                DB::raw("WEEK(created_at) - WEEK('{$startOfMonth->format('Y-m-d')}') + 1 as week_of_month"),
                'account_type',
                DB::raw('COUNT(*) as total')
            )
            ->whereIn('account_type', ['customer', 'supplier'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('week_of_month', 'account_type')
            ->orderBy('week_of_month')
            ->get()
            ->groupBy('week_of_month');

        // تجهيز بيانات activity chart
        $weeks = [];
        $customers = [];
        $suppliersCounts = [];

        for ($w = 1; $w <= 4; $w++) {
            $weeks[] = "الأسبوع {$w}";
            $records = $activity[$w] ?? collect();
            $customers[] = $records->firstWhere('account_type', 'customer')->total ?? 0;
            $suppliersCounts[] = $records->firstWhere('account_type', 'supplier')->total ?? 0;
        }

        // 🔹 Top suppliers revenue
        $topSuppliers = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('business_data', 'products.business_data_id', '=', 'business_data.id')
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->select(
                'business_data.company_name as name',
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('business_data.company_name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        return view('admin.reports', compact(
            'month',
            'reviews',
            'revenue',
            'productsByCategory',
            'salesByCategory',
            'salesBySubCategory',
            'ordersByStatus',
            'weeks',
            'customers',
            'suppliersCounts',
            'topSuppliers'
        ));
    }
}
