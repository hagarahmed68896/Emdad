<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessData;
use App\Models\Order;
use App\Models\FinancialSettlement;
use Illuminate\Http\Request;


class FinancialSettlementController extends Controller
{
public function index(Request $request)
{
    $query = FinancialSettlement::with(['supplier', 'order']);

    // ✅ Filtering
    if ($request->status) {
        $query->where('status', $request->status);
    }

if ($request->search) {
    $query->where(function ($q) use ($request) {
        // ✅ البحث باسم المورد
        $q->whereHas('supplier', function ($sub) use ($request) {
            $sub->where('company_name', 'like', "%{$request->search}%");
        })
        // ✅ البحث برقم التسوية (ID)
        ->orWhere('id', $request->search);
    });
}



    // ✅ Sorting
    if ($request->sort === 'latest') {
        $query->orderBy('created_at', 'desc');
    } elseif ($request->sort === 'oldest') {
        $query->orderBy('created_at', 'asc');
    } elseif ($request->sort === 'amount_high') {
        $query->orderBy('amount', 'desc');
    } elseif ($request->sort === 'amount_low') {
        $query->orderBy('amount', 'asc');
    }

    $settlements = $query->paginate(10);

    // ✅ Summaries
    $totalSettlements  = FinancialSettlement::sum('amount');
    $totalPending      = FinancialSettlement::where('status', 'pending')->sum('amount');
    $totalTransferred  = FinancialSettlement::where('status', 'transferred')->sum('amount');

    $pendingPercentage = $totalSettlements > 0 ? round(($totalPending / $totalSettlements) * 100, 2) : 0;
    $transferredPercentage = $totalSettlements > 0 ? round(($totalTransferred / $totalSettlements) * 100, 2) : 0;

    return view('admin.settlements.index', compact(
        'settlements', 'totalSettlements', 'totalPending', 'totalTransferred',
        'pendingPercentage', 'transferredPercentage'
    ));
}



    public function create()
{
    $suppliers = BusinessData::all();
    $orders = Order::all(); // لو جدول الطلبات اسمه مختلف عدله هنا
    return view('admin.settlements.create', compact('suppliers', 'orders'));
}


public function store(Request $request)
{
    $validated = $request->validate([
        'supplier_id' => 'required|exists:business_data,id',
        'order_id' => 'required|exists:orders,id',
        'amount' => 'required|numeric|min:1',
        'status' => 'required|in:pending,transferred', 
        'settlement_date' => 'required|date',
    ]);

    $settlement = FinancialSettlement::create($validated);

    // If AJAX request, return JSON
    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة التسوية بنجاح ✅',
            'settlement' => $settlement
        ]);
    }


}


    public function edit(FinancialSettlement $settlement)
    {
        $suppliers = BusinessData::all();
            $orders = Order::all(); // لو جدول الطلبات اسمه مختلف عدله هنا

        return view('admin.settlements.edit', compact('settlement', 'suppliers','orders'));
    }

public function update(Request $request, FinancialSettlement $settlement)
{
    $validated = $request->validate([
        'supplier_id' => 'required|exists:business_data,id',
        'amount' => 'required|numeric|min:1',
        'status' => 'required|in:pending,transferred',
        'settlement_date' => 'required|date',
        'order_id' => 'required|exists:orders,id',

    ]);

    $settlement->update($validated);

    // If AJAX request, return JSON
    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'تم تعديل التسوية بنجاح ✅',
            'settlement' => $settlement
        ]);
    }

    return redirect()->route('settlements.index')
                     ->with('success', 'تم تعديل التسوية بنجاح ✅');
}

public function transfer(FinancialSettlement $settlement)
{
    if ($settlement->status === 'pending') {
        $settlement->status = 'transferred';
        $settlement->save();
    }

    return redirect()->route('settlements.index')
        ->with('success', 'تم تحويل التسوية بنجاح');
}


    public function destroy(FinancialSettlement $settlement)
    {
        $settlement->delete();

        return redirect()->route('admin.settlements.index')
                         ->with('success', 'تم حذف التسوية بنجاح ❌');
    }

    public function download()
    {
        $settlements = FinancialSettlement::with('supplier')->get();

        $csv = "رقم التسوية,اسم المورد,رقم الطلب,المبلغ,الحالة,التاريخ\n";
        foreach ($settlements as $s) {
            $csv .= "{$s->id},{$s->supplier->name},{$s->request_number},{$s->amount},{$s->status},{$s->settlement_date}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="settlements.csv"');
    }

    // SettlementController.php
public function bulkTransfer(Request $request)
{
    $ids = json_decode($request->ids, true);
    FinancialSettlement::whereIn('id', $ids)
              ->where('status', 'pending')
              ->update(['status' => 'transferred']);

    return back()->with('success', 'تم تحويل التسويات المحددة');
}

}
