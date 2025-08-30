<?php

namespace App\Http\Controllers;

use App\Models\BusinessData;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class supplierrController extends Controller
{
    // POST /suppliers/{id}/report
    public function report($id, Request $request)
    {
        $supplier = User::findOrFail($id);

        // مثال: تخزن البلاغ في جدول reports
        $supplier->reports()->create([
            'reason' => $request->input('reason', 'غير محدد'),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'تم الإبلاغ عن المورد 🚨'
        ]);
    }

    // POST /suppliers/{id}/ban
    public function ban($id)
    {
        $supplier = User::findOrFail($id);

        $supplier->update([
            'status' => 'banned'
        ]);

        return response()->json([
            'message' => 'تم حظر المورد 🚫'
        ]);
    }
}
