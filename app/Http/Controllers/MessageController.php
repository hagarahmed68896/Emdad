<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QuickReply;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
// app/Http/Controllers/MessageController.php

// app/Http/Controllers/MessageController.php

public function index(Request $request)
{
    $user = Auth::user();

    if ($user->account_type === 'supplier') {
        $conversations = Conversation::with([
            'user', 
            'messages' => function($q) { $q->latest(); },
            'product.supplier.user'
        ])->whereHas('product.supplier.user', function($query) use ($user) {
            $query->where('id', $user->id);
        })->get();

        // Map supplier conversations
        $conversations = $conversations->map(function ($conversation) use ($user) {
            $conversation->full_name = $conversation->user->full_name;
            $conversation->profile_picture = $conversation->user->profile_picture;
            $conversation->company_name = $conversation->user->company_name;

            // Buyer is the "other user" for supplier
            $otherUserId = $conversation->user_id;

            $conversation->is_blocked_by_me = DB::table('user_blocks')
                ->where('blocker_id', $user->id)
                ->where('blocked_id', $otherUserId)
                ->exists();

            return $conversation;
        });

    } else { // customer
        $conversations = Conversation::with([
            'user', 
            'messages' => function($q) { $q->latest(); },
            'product.supplier.user'
        ])->where('user_id', $user->id)->get();

        // Map customer conversations
        $conversations = $conversations->map(function ($conversation) use ($user) {
            $conversation->full_name = $conversation->product->supplier->user->full_name;
            $conversation->profile_picture = $conversation->product->supplier->user->profile_picture;
            $conversation->company_name = $conversation->product->supplier->company_name;

            // Supplier is the "other user" for customer
            $otherUserId = optional($conversation->product?->supplier?->user)->id;

            $conversation->is_blocked_by_me = DB::table('user_blocks')
                ->where('blocker_id', $user->id)
                ->where('blocked_id', $otherUserId)
                ->exists();

            return $conversation;
        });
    }

    $quickReplies = QuickReply::all();
    $openConversationId = null;

    if ($request->has('product_id')) {
        $product = Product::with('supplier')->findOrFail($request->get('product_id'));
        $conversation = Conversation::firstOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $product->id],
                ['status' => 'open'] // ✅ new conversations start as open

        );
        $openConversationId = $conversation->id;
    }

     $unreadMessageCount = 0;
    if (Auth::check()) {
        $unreadMessageCount = Message::whereHas('conversation', function ($query) {
            $query->where('user_id', Auth::id())
                ->orWhereHas('product.supplier', function ($query) {
                    $query->where('user_id', Auth::id());
                });
        })
        ->where('sender_id', '!=', Auth::id())
        ->where('is_read', false)
        ->count();
    }

    return view('messages.index', compact('conversations', 'quickReplies', 'openConversationId','unreadMessageCount'));
}


    public function show(Conversation $conversation)
    {
        $messages = $conversation
            ->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        $product = $conversation->product ? $conversation->product->load('supplier') : null;

        return response()->json([
            'messages' => $messages,
            'product'  => $product,
        ]);
    }

public function store(Request $request, Conversation $conversation)
{
    $request->validate([
        'message' => 'required_without:attachment|nullable|string',
        'quick_reply_id' => 'nullable|exists:quick_replies,id',
        'attachment' => 'required_without:message|nullable|file|max:10240',
    ]);

    $senderId = Auth::id();
    $receiverId = $this->getOtherUserIdInConversation($conversation, $senderId);

    // Check if the sender has blocked the receiver, or if the receiver has blocked the sender.
    $isBlockedBySender = DB::table('user_blocks')
        ->where('blocker_id', $senderId)
        ->where('blocked_id', $receiverId)
        ->exists();

    $isBlockedByReceiver = DB::table('user_blocks')
        ->where('blocker_id', $receiverId)
        ->where('blocked_id', $senderId)
        ->exists();

    if ($isBlockedBySender || $isBlockedByReceiver) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot send messages to a blocked user.'
        ], 403); // Forbidden
    }

   $path = null;

if ($request->hasFile('attachment')) {
    $file = $request->file('attachment');

    // اسم مميز للملف
    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

    // نحرك الملف إلى public/storage/attachments
    $file->move(public_path('storage/attachments'), $filename);

    // نخزن المسار في قاعدة البيانات
    $path = 'attachments/' . $filename;
}


    // Save the user's message
    Message::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $senderId,
        'message' => $request->message,
        'attachment' => $path,
        'type' => $path ? 'attachment' : 'text',
    ]);

    // Handle quick reply logic...
    if ($request->filled('quick_reply_id')) {
        $quickReply = QuickReply::find($request->quick_reply_id);
        if ($quickReply && $quickReply->answer) {
            $supplierUserId = $conversation->product->supplier->user->id ?? null;
            if ($supplierUserId) {
                // Save the automated reply
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $supplierUserId,
                    'message' => $quickReply->answer,
                    'type' => 'text',
                ]);
            }
        }
    }

    // Return the updated list of messages
    $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();
    $product = $conversation->product ? $conversation->product->load('supplier') : null;

    return response()->json([
        'messages' => $messages,
        'product' => $product,
        'success' => true
    ]);
}

/**
 * Helper method to get the ID of the other user in the conversation.
 *
 * @param \App\Models\Conversation $conversation
 * @param int $currentUserId
 * @return int|null
 */
private function getOtherUserIdInConversation($conversation, $currentUserId)
{
    $otherUserId = null;
    if ($conversation->user_id === $currentUserId) {
        // Current user is the customer, other is the supplier
        $otherUserId = optional($conversation->product?->supplier?->user)->id;
    } else {
        // Current user is the supplier, other is the customer
        $otherUserId = $conversation->user_id;
    }
    return $otherUserId;
}

    private function getSupplierId($conversationId)
    {
        $conv = Conversation::find($conversationId);
        return $conv->product?->business_data_id ?? null;
    }

public function uploadAttachment(Request $request)
{
    $request->validate([
        'attachment' => 'required|file|max:10240', // max 10 MB
        'conversation_id' => 'required|exists:conversations,id'
    ]);

    $file = $request->file('attachment');

    // اسم الملف العشوائي
    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

    // مسار الحفظ مباشرة في public/storage/attachments
    $file->move(public_path('storage/attachments'), $filename);

    // المسار اللي هيتخزن في الداتابيز
    $path = 'attachments/' . $filename;

    $message = Message::create([
        'conversation_id' => $request->conversation_id,
        'user_id' => Auth::id(),
        'attachment' => $path,
    ]);

    return response()->json($message);
}


    // -----------------------------
    // 🔹 Ban / Unban Supplier Methods
    // -----------------------------

    // public function toggleBan(User $user)
    // {
    //     $user->status = $user->status === 'banned' ? 'active' : 'banned';
    //     $user->save();

    //     return response()->json([
    //         'success' => true,
    //         'status' => $user->status,
    //         'message' => $user->status === 'banned'
    //             ? 'تم حظر المورد بنجاح'
    //             : 'تم إلغاء الحظر عن المورد'
    //     ]);
    // }

    // public function ban(User $user)
    // {
    //     $user->status = 'banned';
    //     $user->save();

    //     return response()->json([
    //         'success' => true,
    //         'status' => 'banned',
    //         'message' => 'تم حظر المورد بنجاح'
    //     ]);
    // }

    // public function unban(User $user)
    // {
    //     $user->status = 'active';
    //     $user->save();

    //     return response()->json([
    //         'success' => true,
    //         'status' => 'active',
    //         'message' => 'تم إلغاء الحظر عن المورد'
    //     ]);
    // }

public function toggleBlock($id)
{
    $blocker = Auth::user();
    if (!$blocker) {
        return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
    }

    // Decide blocked_id based on blocker account_type
    if ($blocker->account_type === 'supplier') {
        // Supplier blocks the buyer (id is buyer user_id)
        $blockedId = (int) $id;
    } else {
        // Normal user blocks supplier (id is product_id)
        $product = Product::with('supplier.user')->find($id);

        if (!$product || !$product->supplier || !$product->supplier->user) {
            return response()->json(['success' => false, 'message' => 'Supplier not found for this product'], 404);
        }

        $blockedId = $product->supplier->user->id;
    }

    // Check if already blocked
    $isBlocked = DB::table('user_blocks')
        ->where('blocker_id', $blocker->id)
        ->where('blocked_id', $blockedId)
        ->exists();

    if ($isBlocked) {
        DB::table('user_blocks')
            ->where('blocker_id', $blocker->id)
            ->where('blocked_id', $blockedId)
            ->delete();

// 🔹 إعادة تفعيل المحادثات عند إلغاء الحظر
Conversation::where(function ($q) use ($blocker, $blockedId) {
        $q->where('user_id', $blocker->id)
          ->orWhere('user_id', $blockedId);
    })
    ->update(['status' => 'open']);



        return response()->json([
            'success' => true,
            'action' => 'unblocked',
            'message' => 'تم إلغاء الحظر عن المستخدم بنجاح'
        ]);
    } else {
        DB::table('user_blocks')->insert([
            'blocker_id' => $blocker->id,
            'blocked_id' => $blockedId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    User::where('id', $blockedId)->update(['status' => 'banned']);

       // 🔹 غلق المحادثات عند الحظر
Conversation::where(function ($q) use ($blocker, $blockedId) {
        $q->where('user_id', $blocker->id)
          ->orWhere('user_id', $blockedId);
    })
    ->update(['status' => 'closed']);



        return response()->json([
            'success' => true,
            'action' => 'blocked',
            'message' => 'تم حظر المستخدم بنجاح'
        ]);
    }
}





    public function markAsRead($id)
{
    Message::where('conversation_id', $id)
        ->where('sender_id', '!=', Auth::id())
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return response()->json(['status' => 'ok']);
}

}
