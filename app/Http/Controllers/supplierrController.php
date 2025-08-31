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


public function toggleBan(User $user)
{
    if ($user->status === 'banned') {
        // Unban user
        $user->status = 'active'; // أو status سابق إذا عندك تخزين له
    } else {
        // Ban user
        $user->status = 'banned';
    }
    $user->save();

    return redirect()->back()->with('success', 'تم تحديث حالة المستخدم.');
}






}
