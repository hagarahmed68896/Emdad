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
        $query = FinancialSettlement::with('supplier');

        // ✅ فلترة
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $settlements = $query->paginate(10);

        // ✅ ملخصات
        $totalSettlements  = FinancialSettlement::count();
        $totalPending      = FinancialSettlement::where('status', 'معلقة')->sum('amount');
        $totalTransferred  = FinancialSettlement::where('status', 'محوّلة')->sum('amount');

        return view('admin.settlements.index', compact(
            'settlements', 'totalSettlements', 'totalPending', 'totalTransferred'
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
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'request_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'status' => 'required|in:معلقة,محوّلة',
            'settlement_date' => 'required|date',
        ]);

        FinancialSettlement::create($request->all());

        return redirect()->route('admin.settlements.index')
                         ->with('success', 'تمت إضافة التسوية بنجاح ✅');
    }

    public function edit(FinancialSettlement $settlement)
    {
        $suppliers = BusinessData::all();
        return view('admin.settlements.edit', compact('settlement', 'suppliers'));
    }

    public function update(Request $request, FinancialSettlement $settlement)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'request_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'status' => 'required|in:معلقة,محوّلة',
            'settlement_date' => 'required|date',
        ]);

        $settlement->update($request->all());

        return redirect()->route('admin.settlements.index')
                         ->with('success', 'تم تعديل التسوية بنجاح ✅');
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
}
