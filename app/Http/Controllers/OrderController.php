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
        return view('partials.order_tracking');
    }

    public function cancel(Order $order)
{
    if ($order->user_id !== Auth::user()->id) {
        abort(403, 'غير مصرح لك بإلغاء هذا الطلب');
    }

    // Only cancel if not already shipped/delivered
    if ($order->status !== 'processing') {
        return back()->with('error', 'لا يمكن إلغاء الطلب بعد معالجته');
    }

    $order->update(['status' => 'cancelled']);

    return back()->with('success', 'تم إلغاء الطلب بنجاح');
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
