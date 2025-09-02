<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function reportSupplier(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'report_type' => 'required|string',
        ]);

        Report::create([
            'reporter_id'   => Auth::id(),   // الشخص اللي بيعمل البلاغ
            'reported_id'   => $user->id,      // المورد (هو user في الآخر)
            'reported_type' => 'supplier',
            'reason'        => $request->reason,
            'report_type'   => $request->report_type,
        ]);

        return response()->json(['message' => 'تم إرسال البلاغ 🚨']);
    }

    public function reportUser(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        Report::create([
            'reporter_id'   => Auth::id(),   // مين عمل البلاغ
            'reported_id'   => $user->id,      // المستخدم المبلغ عنه
            'reported_type' => 'user',
            'reason'        => $request->reason,
            'report_type'   => $request->report_type,
        ]);

        return response()->json(['message' => 'تم إرسال البلاغ 🚨']);
    }
}


