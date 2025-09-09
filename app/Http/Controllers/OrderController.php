<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderStatusUpdatedNotification;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
                $orders = Auth::user()->orders()->with('orderItems.product.subCategory.category')->get();
        return view('partials.order_tracking', compact('orders'));
    }


public function updateStatus(Request $request, Order $order)
{
    // Ensure the user is a supplier
    if (Auth::user()->account_type !== 'supplier') {
        abort(403, 'Unauthorized action.');
    }

    // Get the supplier's business
    $business = Auth::user()->business;
    if (!$business) {
        abort(403, 'You are not a registered supplier.');
    }

    // Get all products for this supplier
    $supplierProductIds = $business->products()->pluck('id')->toArray();
    $orderProductIds = $order->orderItems->pluck('product_id')->toArray();

    // Check if this order contains any of the supplier's products
    if (!array_intersect($supplierProductIds, $orderProductIds)) {
        abort(403, 'You cannot update this order.');
    }

    // Validate status
    $request->validate([
        'status' => 'required|in:processing,shipped,delivered',
    ]);

    // Update order status
    $order->status = $request->input('status');
    $order->save();

    // Notify the customer
    $customer = $order->user;
    $settings = $customer->notification_settings ?? [];

    if (
        isset($settings['receive_in_app']) && $settings['receive_in_app'] &&
        isset($settings['order_status_updates']) && $settings['order_status_updates']
    ) {
        $customer->notify(new \App\Notifications\OrderStatusUpdatedNotification($order));
    }

    return back()->with('success', 'Order status updated successfully.');
}

//     public function cancel(Order $order)
// {
//     if ($order->user_id !== Auth::user()->id) {
//         abort(403, 'غير مصرح لك بإلغاء هذا الطلب');
//     }

//     // Only cancel if not already shipped/delivered
//     if ($order->status !== 'pending') {
//         return back()->with('error', 'لا يمكن إلغاء الطلب بعد معالجته');
//     }

//     $order->update(['status' => 'cancelled']);

//     return back()->with('success', 'تم إلغاء الطلب بنجاح');
// }
// OrderController.php
public function cancel(Order $order)
{
    // Prevent cancel if already shipped/delivered
    if (in_array($order->status, ['shipped', 'delivered'])) {
        return back()->with('error', 'لا يمكن إلغاء هذا الطلب بعد شحنه أو تسليمه.');
    }

    $order->status = 'cancelled'; // must match your stepMap spelling
    $order->save();

    return redirect()->route('order.show')->with('success', 'تم إلغاء الطلب بنجاح.');
}


    /**
     * Show the form for creating a new resource.
     */
 public function show(Order $order)
    {
        return view('partials.order_tracking', compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
