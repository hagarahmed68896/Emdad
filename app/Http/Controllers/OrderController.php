<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

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
