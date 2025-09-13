<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Models\Notification;


class MessagesController extends Controller
{
    public function index(Request $request)
    {
        $query = Conversation::query()
            ->with(['user', 'supplier', 'messages','reports' => function($q) {
                $q->latest()->first();
            }]);

        // ✅ البحث
if ($request->filled('search')) {
    $search = $request->search;

    $query->whereHas('user', fn($q) => 
        $q->where('full_name', 'like', "%$search%")
    )->orWhereHas('product.supplier.user', fn($q) => 
        $q->where('full_name', 'like', "%$search%")
    );
}


        // ✅ الفلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

      // ✅ الترتيب
switch ($request->sort) {
    case 'name_asc':
        $query->join('users as u', 'u.id', '=', 'conversations.user_id')
              ->orderBy('u.full_name', 'asc');
        break;

    case 'name_desc':
        $query->join('users as u', 'u.id', '=', 'conversations.user_id')
              ->orderBy('u.full_name', 'desc');
        break;

    case 'oldest':
        $query->orderBy('created_at', 'asc');
        break;

    case 'latest':
    default:
        $query->orderBy('created_at', 'desc');
        break;
}


        $conversations = $query->paginate(10);

        return view('admin.messages.index', compact('conversations'))
            ->with([
                'statusFilter' => $request->status,
                'sortFilter' => $request->sort,
                'search' => $request->search,
            ]);
    }

    public function show(Conversation $conversation)
    {
        $conversation->load(['messages.sender']);
        return view('admin.messages.show', compact('conversation'));
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();
        return redirect()->route('admin.messages.index')->with('success', 'تم حذف المحادثة');
    }

    public function close(Conversation $conversation)
    {
        $conversation->update(['status' => 'closed']);
        return redirect()->back()->with('success', 'تم إغلاق المحادثة');
    }

    public function action(Request $request, Conversation $conversation)
{
   $targetUserId = $request->input('target') === 'client' 
                ? $conversation->user_id 
                : $conversation->supplier?->user?->id ?? null;


switch ($request->input('action')) {
    case 'close':
        $conversation->update([
            'status' => 'closed',
            'can_send_messages' => false
        ]);
        Notification::create([
            'type' => 'admin_action', // or whatever you want to classify this notification
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id' => $targetUserId,
            'data' => ['message' => 'تم إغلاق المحادثة من قبل الإدارة.'],
        ]);
        break;

    case 'under_review':
        $conversation->update([
            'status' => 'under_review',
            'block_until' => now()->addDays(3),
            'can_send_messages' => false
        ]);
Notification::create([
    'type' => 'admin_action',
    'notifiable_type' => \App\Models\User::class,
    'notifiable_id' => $targetUserId,
    'data' => [
        'message' => 'تم وضع المحادثة تحت المراجعة، لا يمكن إرسال رسائل لمدة 3 أيام.'
    ],
]);

        break;

    case 'warn_only':
        Notification::create([
            'type' => 'admin_action', // or whatever you want to classify this notification
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id' => $targetUserId,
            'data' => ['message' => 'تم توجيه تحذير لك من قبل الإدارة.'],
        ]);
        break;
}


    return response()->json(['success' => true]);
}

public function updateStatus(Request $request, $id)
{
    $conversation = Conversation::findOrFail($id);
    $conversation->status = $request->status; // "under_review"
    $conversation->save();

    return redirect()->back()->with('success', 'تم تحديث حالة المحادثة بنجاح');
}

}
