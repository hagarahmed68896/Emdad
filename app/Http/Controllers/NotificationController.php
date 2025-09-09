<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = []; 
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
    $notification = Auth::user()->notifications()->findOrFail($id);

    if ($notification) {
        $notification->markAsRead();
    }

    return response()->json([
        'success' => true,
        'message' => 'Notification marked as read'
    ]);
    }

    public function getAll()
{
    $notifications = Auth::user()->notifications()->latest()->take(50)->get();

    return response()->json([
        'html' => view('partials.notifications_popup', compact('notifications'))->render()
    ]);
}

}
