<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QuickReply;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $conversations = Conversation::with([
            'user',
            'messages' => function($q) { $q->latest(); },
            'product.supplier.user'
        ])->get();

        $quickReplies  = QuickReply::all();

        $openConversationId = null;

        if ($request->has('product_id')) {
            $product = Product::with('supplier')->findOrFail($request->get('product_id'));

            // check if conversation exists or create it
            $conversation = Conversation::firstOrCreate(
                [
                    'user_id'    => Auth::id(),
                    'product_id' => $product->id,
                ]
            );

            $openConversationId = $conversation->id;
        }

        return view('messages.index', compact('conversations', 'quickReplies', 'openConversationId'));
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
    // Validate the request to ensure a message is present and a quick reply ID can be sent
    $request->validate([
        'message' => 'required|string',
        'quick_reply_id' => 'nullable|exists:quick_replies,id',
    ]);

    // Save the user's message first
    $userMessage = Message::create([
        'conversation_id' => $conversation->id,
        'sender_id' => Auth::id(),
        'message' => $request->message,
        'type' => 'text',
    ]);

    // Check for a quick reply and its answer to create an automated response
    if ($request->filled('quick_reply_id')) {
        $quickReply = QuickReply::find($request->quick_reply_id);
        if ($quickReply && $quickReply->answer) {
            // Find the supplier's user ID associated with this conversation's product
            $supplierUserId = $conversation->product->supplier->user->id ?? null;

            if ($supplierUserId) {
                // Save the automated reply with the correct sender ID
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $supplierUserId,
                    'message' => $quickReply->answer,
                    'type' => 'text',
                ]);
            }
        }
    }

    // After creating both messages (if applicable),
    // get the full, updated list of messages for the conversation
    // and send it back to the front end to update the UI
    $messages = $conversation
        ->messages()
        ->with('sender')
        ->orderBy('created_at')
        ->get();

    $product = $conversation->product ? $conversation->product->load('supplier') : null;

    return response()->json([
        'messages' => $messages,
        'product' => $product,
    ]);
}

    private function getSupplierId($conversationId)
    {
        $conv = Conversation::find($conversationId);
        return $conv->product?->business_data_id ?? null;
    }

    public function uploadAttachment(Request $request)
{
    // Validate the file
    $request->validate([
        'attachment' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,mp4|max:20480', // Max 20MB
        'conversation_id' => 'required|exists:conversations,id',
    ]);

    $conversation = Conversation::findOrFail($request->conversation_id);
    $senderId = Auth::id();
    $file = $request->file('attachment');

    // Store the file in a storage disk and get its path
    $path = $file->store('chat_attachments', 'public');

    // Create a new message with the attachment path
    $message = Message::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $senderId,
        'message' => $path, // Save the file path as the message content
        'type' => 'attachment', // Add a 'type' to distinguish it from text messages
    ]);

    // Return the created message to the frontend
    return response()->json(['message' => $message->load('sender')]);
}
}
