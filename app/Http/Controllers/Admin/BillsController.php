<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

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

    public function create()
    {
        return view('admin.bills.create');
    }


public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'customer_name' => 'required',
        'order_number'  => 'required',
        'payment_way'   => 'required',
        'total_price'   => 'required|numeric',
        'status'        => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('full_name', $request->customer_name)
                ->where('account_type', 'customer')
                ->first();

    if (!$user) {
        return response()->json(['errors' => ['customer_name' => ['العميل غير موجود']]], 422);
    }

    $order = Order::where('order_number', $request->order_number)->first();

    if (!$order) {
        return response()->json(['errors' => ['order_number' => ['الطلب غير موجود']]], 422);
    }

    $bill = Bill::create([
        'user_id'     => $user->id,
        'order_id'    => $order->id,
        'payment_way' => $request->payment_way,
        'total_price' => $request->total_price,
        'status'      => $request->status,
    ]);

    $bill->bill_number = $bill->id;
    $bill->save();

    return response()->json(['message' => '✅ تمت إضافة الفاتورة بنجاح!']);
}


public function edit(Bill $invoice)
{
    return view('admin.bills.edit', [
        'bill' => $invoice
    ]);
}



public function update(Request $request, Bill $invoice)
{
    $validated = $request->validate([
        'customer_name' => 'required',
        'order_number'  => 'required',
        'payment_way'   => 'required',
        'total_price'   => 'required|numeric',
        'status'        => 'required',
    ]);

    $user = User::where('full_name', $validated['customer_name'])
                ->where('account_type', 'customer')
                ->first();

    if (!$user) {
        return response()->json(['errors' => ['customer_name' => ['لم يتم العثور على هذا العميل.']]], 422);
    }

    $order = Order::where('order_number', $validated['order_number'])->first();
    if (!$order) {
        return response()->json(['errors' => ['order_number' => ['لم يتم العثور على هذا الطلب.']]], 422);
    }

    $invoice->update([
        'user_id'     => $user->id,
        'order_id'    => $order->id,
        'payment_way' => $validated['payment_way'],
        'total_price' => $validated['total_price'],
        'status'      => $validated['status'],
    ]);

    return response()->json(['message' => 'تم تحديث الفاتورة بنجاح!']);
}






    
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
