<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index() {
        $tickets = Ticket::where('user_id', Auth::id())->latest()->get();
        return view('tickets.index', compact('tickets'));
    }

    public function store(Request $request) {
        $request->validate([
            'subject' => 'required|string|max:255',
            'type' => 'required|string',
            'message' => 'required|string',
        ]);

        Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'type' => $request->type,
            'message' => $request->message,
        ]);

        return back()->with('success', 'تم فتح التذكرة بنجاح');
    }

    public function show(Ticket $ticket) {
        abort_if($ticket->user_id !== Auth::id(), 403); // حماية
        $ticket->load('replies.user');
        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket) {
        abort_if($ticket->user_id !== Auth::id(), 403);

        $request->validate(['message' => 'required|string']);
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'message'   => $request->message,
        ]);

        return back()->with('success', 'تم إضافة الرد');
    }
}


