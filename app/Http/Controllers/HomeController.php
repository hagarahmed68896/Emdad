<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Offer;
use App\Models\OrderItem; // ⬅️ Add this import
use Carbon\Carbon; // ⬅️ Add this import

class HomeController extends Controller
{
    // ⬅️ Add Request $request parameter
    public function index(Request $request)
    {
        $categories = Category::all();
        $onOfferProducts = Product::with('subCategory.category')
    ->whereHas('offer', function ($query) {
        $query->where(function ($q) {
            $q->whereNull('offer_end')
              ->orWhere('offer_end', '>', now());
        });
    })
    ->get();


        $featuredProducts = Product::with('subCategory.category')
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $favorites = collect();
        $products = collect();
        $offers = collect();
        
        // ⬅️ Initialize these variables to prevent "Undefined variable" errors
        $currentMonthSales = [];
        $lastMonthSales = [];
        $sales = collect();

        if (Auth::check()) {
            $favorites = Auth::user()->favorites()->with('product.subCategory.category')->get();
$business = Auth::user()->business;

$offers = collect(); // default empty

if ($business) {
    $offers = Offer::whereHas('product', function ($q) use ($business) {
        $q->where('business_data_id', $business->id);
    })
    ->with(['product.subCategory.category'])
    ->paginate(20);
}

            if (Auth::user()->account_type === 'supplier') {
                $business = Auth::user()->business;

                if ($business) {
                    $products = $business->products()->get();

                    // ✅ START OF NEW SALES CHART DATA LOGIC ✅
                    $supplierId = $business->id;

                    // Current month sales grouped by week-of-month
                    $currentMonthSales = OrderItem::whereHas('product', function ($q) use ($supplierId) {
                            $q->where('business_data_id', $supplierId);
                        })
                        ->whereMonth('created_at', now()->month)
                        ->selectRaw('FLOOR((DAY(created_at)-1)/7)+1 as week_of_month, SUM(unit_price * quantity) as total')
                        ->groupBy('week_of_month')
                        ->pluck('total', 'week_of_month');

                    // Last month sales grouped by week-of-month
                    $lastMonthSales = OrderItem::whereHas('product', function ($q) use ($supplierId) {
                            $q->where('business_data_id', $supplierId);
                        })
                        ->whereMonth('created_at', now()->subMonth()->month)
                        ->selectRaw('FLOOR((DAY(created_at)-1)/7)+1 as week_of_month, SUM(unit_price * quantity) as total')
                        ->groupBy('week_of_month')
                        ->pluck('total', 'week_of_month');

                    // Fill missing weeks with 0 and format
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

                    // 🔹 Apply filters to sales query
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
                    // ✅ END OF NEW SALES DATA LOGIC ✅
                }
            }
        }

        $cartItems = collect();
        $cart = null;

        if (Auth::check()) {
            $cart = Auth::user()->cart;
        } else {
            $sessionId = Session::getId();
            $cart = Cart::where('session_id', $sessionId)
                        ->where('status', 'active')
                        ->first();
        }

        if ($cart) {
            $cartItems = $cart->items()->with('product')->get();
        }

        $notifications = collect();
        $unreadNotificationCount = 0;

        if (Auth::check()) {
            $notifications = Auth::user()->notifications()->latest()->take(5)->get();
            $unreadNotificationCount = Auth::user()->unreadNotifications->count();
        }

        if (Auth::check() && Auth::user()->account_type === 'admin') {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect('/');
        }

        $supplierCategories = collect();
        $supplierCategoryCount = 0;

        if (Auth::check() && Auth::user()->account_type === 'supplier') {
            $business = Auth::user()->business;

            if ($business) {
                $supplierCategories = Category::whereHas('subCategories.products', function ($query) use ($business) {
                    $query->where('business_data_id', $business->id);
                })->distinct()->get();

                $supplierCategoryCount = $supplierCategories->count();
            }
        }

        return view('layouts.app', compact(
            'categories',
            'onOfferProducts',
            'featuredProducts',
            'favorites',
            'products',
            'cartItems',
            'notifications',
            'unreadNotificationCount',
            'offers',
            'supplierCategories',
            'supplierCategoryCount',
            'currentMonthSales', // ⬅️ Add new variables to compact
            'lastMonthSales',
            'sales'
        ));
    }
}