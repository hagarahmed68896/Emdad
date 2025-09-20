<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfitSetting;
use Illuminate\Http\Request;

class ProfitController extends Controller
{
    public function index()
    {
        $profit = ProfitSetting::first(); // دايماً هيكون فيه قيمة واحدة
        return view('admin.profit', compact('profit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        ProfitSetting::updateOrCreate(
            ['id' => ProfitSetting::first()->id ?? null],
            ['percentage' => $request->percentage]
        );

        return redirect()->back()->with('success', 'تم تحديث نسبة الأرباح بنجاح');
    }
}

