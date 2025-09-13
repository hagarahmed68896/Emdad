<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Report a user within a conversation.
     * The reported user's ID is passed in the route.
     * The reporter is the authenticated user.
     */
    public function reportUser(Request $request, User $user)
    {
        $reporter = Auth::user();

        // 🛑 Prevent a user from reporting themselves
        if ($reporter->id === $user->id) {
            return response()->json(['message' => __('messages.cannot_report_self')], 400);
        }

        $request->validate([
            'reason' => 'required|string|max:255',
            'report_type' => 'required|string',
        ]);
        
        // Find the specific conversation between the two users
        $conversation = Conversation::where('user_id', $reporter->id)
            ->whereHas('product.supplier', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orWhere(function ($q) use ($reporter, $user) {
                $q->where('user_id', $user->id)
                    ->whereHas('product.supplier', function ($q) use ($reporter) {
                        $q->where('user_id', $reporter->id);
                    });
            })
            ->first();

        if (!$conversation) {
            return response()->json(['message' => __('messages.no_conversation_found')], 404);
        }

        // Create the report
        Report::create([
            'reporter_id'     => $reporter->id,
            'reported_id'     => $user->id,
            'reason'          => $request->reason,
            'report_type'     => $request->report_type,
            'reported_type'   => 'user', // Add this line
            'conversation_id' => $conversation->id,
        ]);

        // ✅ Update conversation status
        $conversation->update(['status' => 'reported']);

        return response()->json(['message' => __('messages.report_sent_success')], 200);
    }
}