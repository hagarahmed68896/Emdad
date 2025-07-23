<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use Barryvdh\DomPDF\Facade\Pdf;

class BillsController extends Controller
{
 public function index(Request $request)
{
    // إحصائيات الفواتير
    $totalInvoices = Bill::count();
    $paidInvoices = Bill::where('status', 'payment')->count();
    $unpaidInvoices = Bill::where('status', 'not payment')->count();

    $query = Bill::query()->with('user');

    // ✅ البحث
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('bill_number', 'like', "%{$search}%")
              ->orWhereHas('user', function ($q2) use ($search) {
                  $q2->where('full_name', 'like', "%{$search}%");
              })
              ->orWhereHas('order', function ($q2) use ($search) {
                  $q2->where('order_number', 'like', "%{$search}%");
              });
        });
    }

    // ✅ التصفية حسب الحالة مع اسم الجدول
    if ($request->filled('status')) {
        $query->where('bills.status', $request->status);
    }

    // ✅ الترتيب (يستخدم قيمة الـ input وليس filled)
    switch ($request->input('sort')) {
        case 'full_name_asc':
            $query->join('users', 'bills.user_id', '=', 'users.id')
                  ->orderBy('users.full_name', 'asc')
                  ->select('bills.*');
            break;
        case 'latest':
            $query->orderBy('created_at', 'desc');
            break;
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    // ✅ اجلب النتائج مع صفحة
    $perPage = $request->input('per_page', 10);
    $invoices = $query->paginate($perPage);

    return view('admin.bills', [
        'totalInvoices' => $totalInvoices,
        'paidInvoices' => $paidInvoices,
        'unpaidInvoices' => $unpaidInvoices,
        'invoices'     => $invoices,
        'statusFilter' => $request->input('status'),
        'sortFilter'   => $request->input('sort'),
    ]);
}




    // public function index(Request $request)
    // {
    //     // إحصائيات الفواتير
    //     $totalInvoices = Bill::count();
    //     $paidInvoices = Bill::where('status', 'payment')->count();
    //     $unpaidInvoices = Bill::where('status', 'not payment')->count();

    //     // فلترة البيانات
    //     $invoicesQuery = Bill::query()->with('user');

    //     // فلترة بالحالة إن وجدت
    //     if ($request->filled('status') && in_array($request->status, ['payment', 'not payment'])) {
    //         $invoicesQuery->where('status', $request->status);
    //     }

    //     // فلترة بطريقة الدفع إن وجدت
    //     if ($request->filled('payment_way')) {
    //         $invoicesQuery->where('payment_way', $request->payment_way);
    //     }

    //     // فلترة بالبحث إن وجد
    //     if ($request->filled('search')) {
    //         $search = $request->search;

    //         $invoicesQuery->where(function ($q) use ($search) {
    //             $q->where('bill_number', 'like', "%{$search}%")
    //               ->orWhereHas('user', function ($q) use ($search) {
    //                   $q->where('full_name', 'like', "%{$search}%")
    //                     ->orWhere('email', 'like', "%{$search}%");
    //               });
    //         });
    //     }

    //     // جلب النتائج مع ترقيم الصفحات
    //     $invoices = $invoicesQuery->latest()->paginate(10);

    //     return view('admin.bills', [
    //         'totalInvoices' => $totalInvoices,
    //         'paidInvoices' => $paidInvoices,
    //         'unpaidInvoices' => $unpaidInvoices,
    //         'invoices' => $invoices,
    //         'request' => $request, // مهم لإرجاع قيم الفلتر للواجهة
    //     ]);
    // }

    
    
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:bills,id',
        ]);

        Bill::whereIn('id', $request->invoice_ids)->delete();

        return redirect()->route('admin.bills.index')
            ->with('success', 'تم حذف الفواتير المحددة بنجاح.');
    }

    public function downloadPdf($id)
    {
        $invoice = Bill::with('user', 'order.orderItems')->findOrFail($id);

        $pdf = Pdf::loadView('admin.bill_pdf', compact('invoice'))
            ->setOptions([
                'defaultFont' => 'Amiri',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download('فاتورة-' . $invoice->bill_number . '.pdf');
    }

    public function showPdf($id)
    {
        $invoice = Bill::with('user', 'order.orderItems')->findOrFail($id);

        $pdf = Pdf::loadView('admin.bill_pdf', compact('invoice'))
            ->setOptions([
                'defaultFont' => 'Amiri',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('فاتورة-' . $invoice->bill_number . '.pdf');
    }
}
