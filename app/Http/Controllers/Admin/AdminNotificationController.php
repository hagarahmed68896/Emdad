<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Make sure to import the User model
use Illuminate\Support\Facades\Notification; // Import the Notification facade
use App\Notifications\AdminUserNotification;

class AdminNotificationController extends Controller
{
public function index(Request $request)
{
    $query = AdminNotification::latest();

    if ($search = $request->input('search')) {
        $query->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
    }

    if ($status = $request->input('status')) {
        $query->where('status', $status);
    }

    // THIS IS THE KEY: paginate the query builder
    $notifications = $query->paginate(10)->withQueryString();

    // Stats
    $total = AdminNotification::count();
    $pendingCount = AdminNotification::where('status', 'pending')->count();
    $sentCount = AdminNotification::where('status', 'sent')->count();

    $pendingPercent = $total > 0 ? round(($pendingCount / $total) * 100, 2) : 0;
    $sentPercent = $total > 0 ? round(($sentCount / $total) * 100, 2) : 0;

    return view('admin.notifications.index', compact(
        'notifications',
        'total',
        'pendingCount', 'sentCount',
        'pendingPercent', 'sentPercent'
    ));
}




    // ... (Your other methods like create, store, etc., remain the same)

    // Add a new method for bulk deletion
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:admin_notifications,id',
        ]);

        AdminNotification::destroy($request->input('ids'));

        return redirect()->route('admin.notifications.index')
                         ->with('success', 'تم حذف الإشعارات المحددة بنجاح.');
    }


    public function create()
    {
        return view('admin.notifications.create');
    }




public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category' => 'required|in:client,supplier',
        'notification_type' => 'required|in:alert,offer,info',
        'status' => 'required|in:sent,pending',
    ]);

    $users = User::where('account_type', $request->category)->get();

    // Send Laravel Notification to users
    // Notification::send($users, new AdminUserNotification($request->title, $request->content));

    // Store a record in admin_notifications table (safe, no notifiable_type required)
    AdminNotification::create([
        'id' => Str::uuid(),
        'title' => $request->title,
        // 'content' => $request->content,
        'category' => $request->category,
        'notification_type' => $request->notification_type,
        'status' => 'sent',
        'data' => [
            'title' => $request->title,
            // 'message' => $request->content,
        ],
    ]);

    return redirect()->route('admin.notifications.index')->with('success', 'تم إرسال الإشعار بنجاح.');
}




    public function edit($id)
    {
        $notification = AdminNotification::findOrFail($id);
        return view('admin.notifications.edit', compact('notification'));
    }

    public function update(Request $request, $id)
    {
        $notification = AdminNotification::findOrFail($id);

        $request->validate([
            'title'             => 'required|string|max:255',
            'content'           => 'required|string',
            'category'          => 'required|in:client,supplier',
            'notification_type' => 'required|in:alert,offer,info',
            'status'            => 'required|in:sent,pending',
        ]);

        $notification->update([
            'title'            => $request->input('title'),
            'content'          => $request->input('content'),
            'category'         => $request->input('category'),
            'notification_type'=> $request->input('notification_type'),
            'status'           => $request->input('status'),
            'data'             => [
                'message' => $request->input('content'),
                'title'   => $request->input('title'),
            ],
        ]);

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'تم تعديل الإشعار');
    }

    public function destroy($id)
    {
        AdminNotification::findOrFail($id)->delete();

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'تم حذف الإشعار');
    }
}
