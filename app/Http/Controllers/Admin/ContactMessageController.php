<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index(Request $request)
{
    $query = ContactMessage::query();

    if ($search = $request->input('search')) {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%");
    }

    $messages = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.contact_messages.index', compact('messages'));
}

}
