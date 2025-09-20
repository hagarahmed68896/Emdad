<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'type'    => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = ContactMessage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رسالتك بنجاح ✅',
            'id'      => $contact->id,
        ], 200);
    }
}
