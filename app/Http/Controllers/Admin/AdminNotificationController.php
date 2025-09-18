<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminNotification;
use App\Notifications\AdminUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log; // Add this line for debugging
use Illuminate\Support\Facades\DB; // Add this for database transactions

class AdminNotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
  public function index(Request $request)
    {
        // Use a single query for the table data
        $query = AdminNotification::latest();

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
        }

        // Apply status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Apply category filter (if you add this to the form)
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Paginate the results
        $Notifications = $query->paginate(10)->withQueryString();

        // Calculate statistics from the same table
        $total = AdminNotification::count();
        $pendingCount = AdminNotification::where('status', 'pending')->count();
        $sentCount = AdminNotification::where('status', 'sent')->count();

        // Calculate percentages
        $pendingPercent = $total > 0 ? round(($pendingCount / $total) * 100, 2) : 0;
        $sentPercent = $total > 0 ? round(($sentCount / $total) * 100, 2) : 0;

        // Pass data to the view
        return view('admin.notifications.index', compact(
            'Notifications',
            'total',
            'pendingCount',
            'sentCount',
            'pendingPercent',
            'sentPercent'
        ));
    }
    
    // ... other methods



    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Store a newly created notification.
     */


public function store(Request $request)
{
    $request->validate([
        'title'             => 'required|string|max:255',
        'content'           => 'required|string',
        'category'          => 'required|in:customer,supplier',
        'notification_type' => 'required|in:alert,offer,info',
        'status'            => 'required|in:sent,pending',
    ]);

    $notification = AdminNotification::create($request->all());

    // ✅ 3. Get users from selected category AND only those who accept notifications
$users = User::where('account_type', $request->category)
             ->whereJsonContains('notification_settings->receive_chat', true)
             ->get();


// ✅ 4. If status is 'sent', actually notify users
if ($request->status === 'sent' && $users->isNotEmpty()) {
    Notification::send($users, new AdminUserNotification(
        $request->input('title'),
        $request->input('content'),
        $request->input('category'),
        $request->input('notification_type'),
        $request->input('status')
    ));
}


    // 👇 Return back to the form with a success flag
    return view('admin.notifications.create', [
        'success' => 'تم حفظ الإشعار بنجاح ✅'
    ]);
}

  // ================== New: Edit ==================
    public function edit($id)
    {
        $notification = AdminNotification::findOrFail($id);
        return view('admin.notifications.edit', compact('notification'));
    }
  // ================== New: Update ==================
 public function update(Request $request, $id)
{
    $request->validate([
        'title'             => 'required|string|max:255',
        'content'           => 'required|string',
        'category'          => 'required|in:customer,supplier',
        'notification_type' => 'required|in:alert,offer,info',
        'status'            => 'required|in:sent,pending',
    ]);

    $notification = AdminNotification::findOrFail($id);
    $notification->update($request->all());

    // رجّع على صفحة التعديل ومعاها session flash
 return view('admin.notifications.edit', [
    'notification' => $notification,
    'success' => 'تم تعديل الإشعار بنجاح ✅'
]);

}


    // ================== New: Destroy ==================
    public function destroy($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.notifications.index')->with('success', 'تم حذف الإشعار بنجاح ❌');
    }

    // ================== New: Bulk Delete ==================
    public function bulkDelete(Request $request)
{
    $ids = $request->input('ids', []);

    if (!empty($ids)) {
        AdminNotification::whereIn('id', $ids)->delete();
        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم حذف الإشعارات المحددة ✅');
    }

    return redirect()->route('admin.notifications.index')
        ->with('error', 'لم يتم اختيار أي إشعار ❌');
}

// In App\Http\Controllers\Admin\AdminNotificationController.php

// In App\Http\Controllers\Admin\AdminNotificationController.php


// ... other code

public function toggleStatus(AdminNotification $notification) // Change the type-hint here
{
    // Toggle the status between 'sent' and 'not_sent'
    if ($notification->status === 'sent') {
        $notification->status = 'pending';
    } else {
        $notification->status = 'sent';
    }
    
    $notification->save();

    // Redirect back to the previous page with a success message
    return back()->with('success', 'Notification status updated successfully.');
}

}