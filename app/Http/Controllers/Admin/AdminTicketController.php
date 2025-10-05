<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTicketController extends Controller
{
public function index(Request $request)
{
    $query = Ticket::with('user')->latest();

    // ✅ البحث بالموضوع
    if ($request->filled('search')) {
        $query->where('subject', 'like', '%' . $request->search . '%');
    }

    // ✅ فلترة بالحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // ✅ فلترة بالنوع
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    $tickets = $query->paginate($request->get('per_page', 10))
                     ->appends($request->query()); // علشان يحتفظ بالفلاتر في pagination

    return view('admin.tickets.index', compact('tickets'));
}



    public function show(Ticket $ticket) {
        $ticket->load('user', 'replies.user');
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket) {
        $request->validate(['message' => 'required|string']);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(), // الأدمن
            'message'   => $request->message,
        ]);

        return back()->with('success', 'تم إرسال الرد للعميل');
    }

    public function close(Ticket $ticket) {
        $ticket->update(['status' => 'closed']);
        return back()->with('success', 'تم إغلاق التذكرة');
    }
}

