<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrdersController extends Controller
{
public function index(Request $request)
{
    // إحصائيات عامة
    $totalOrders = Order::count();
    $completedOrders = Order::where('status', 'completed')->count();
    $processingOrders = Order::where('status', 'processing')->count();
    $cancelledOrders = Order::where('status', 'cancelled')->count();
$completedPercentage = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0;
$processingPercentage = $totalOrders > 0 ? round(($processingOrders / $totalOrders) * 100, 2) : 0;
$cancelledPercentage = $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100, 2) : 0;

    // بناء الاستعلام
    $query = Order::query()->with(['user', 'orderItems']);

    // فلترة بالحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // فلترة بحث (مثلاً بالاسم)
    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    // الترتيب
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'full_name_asc':
                $query->join('users', 'orders.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'asc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'total_amount_desc':
                $query->orderBy('total_amount', 'desc');
                break;
            default:
                $query->latest();
        }
    } else {
        $query->latest();
    }

    // تنفيذ الاستعلام مع Pagination
    $orders = $query->paginate(10);

    return view('admin.orders.index', [
        'orders' => $orders,
        'totalOrders' => $totalOrders,
        'completedOrders' => $completedOrders,
        'processingOrders' => $processingOrders,
        'cancelledOrders' => $cancelledOrders,
        'cancelledPercentage' => $cancelledPercentage,
        'processingPercentage' => $processingPercentage,
        'completedPercentage' => $completedPercentage,
        'statusFilter' => $request->input('status'),
        'sortFilter' => $request->input('sort'),
        'search' => $request->input('search'),
    ]);
}


public function exportCsv(Request $request)
{
    $query = Order::query()->with(['user', 'orderItems']);

    // الفلاتر
    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'full_name_asc':
                $query->join('users', 'orders.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'asc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->latest();
        }
    } else {
        $query->latest();
    }

    $ordersToExport = $query->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=orders_export_" . now()->format('Ymd_His') . ".csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($ordersToExport) {
        $file = fopen('php://output', 'w');
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // الرؤوس
        fputcsv($file, [
            'ID',
            'رقم الطلب',
            'اسم العميل',
            'أسماء المنتجات',
            'عدد المنتجات',
            'إجمالي السعر',
            'وسيلة الدفع',
            'الحالة',
            'تاريخ الإنشاء',
        ]);

        foreach ($ordersToExport as $order) {
            fputcsv($file, [
                $order->id,
                $order->order_number ?? '-',
                $order->user->name ?? '-',
                $order->orderItems->pluck('product_name')->join(', '),
                $order->orderItems->sum('quantity'),
                number_format($order->calculateTotalAmount(), 2) . ' ر.س',
                $order->payment_way ?? '-',
                match($order->status) {
                    'completed' => 'مكتمل',
                    'pending' => 'جاري',
                    'cancelled' => 'ملغي',
                    'returned' => 'ارجاع',
                    default => 'غير معروف',
                },
                $order->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);
    };

    return new StreamedResponse($callback, 200, $headers);
}

}
