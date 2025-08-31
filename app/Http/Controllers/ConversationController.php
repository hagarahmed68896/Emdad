<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    // DELETE /conversations/{id}

    public function destroy($id)
{
    $conversation = Conversation::findOrFail($id);

    // ✅ احذف الرسائل المرتبطة
    $conversation->messages()->delete();

    // ✅ احذف المحادثة نفسها
$conversation->forceDelete();

    return response()->json(['message' => 'تم حذف المحادثة 🗑️']);
}


}
