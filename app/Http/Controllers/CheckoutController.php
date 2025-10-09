<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // Validation Rules
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|in:paypal,card',
        ];

        if ($request->payment_method === 'paypal') {
            $rules['paypal_email'] = 'required|email';
        }

        if ($request->payment_method === 'card') {
            $rules = array_merge($rules, [
                'card_number' => 'required|digits:16',
                'card_name' => 'required|string|max:100',
                'expiry_date' => 'required|string|max:5',
                'cvc' => 'required|digits_between:3,4',
            ]);
        }

        $validated = $request->validate($rules);
        $user = Auth::user();
        $userId = $user->id;

        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'السلة فارغة، لا يمكن إتمام الطلب.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Step 1: Create a new Order with temporary total
            $order = Order::create([
                'user_id' => $userId,
                'first_name' => $validated['first_name'] ?? $user->first_name,
                'last_name' => $validated['last_name'] ?? $user->last_name,
                'phone_number' => $validated['phone'] ?? $user->phone, 
                'email' => $validated['email'] ?? $user->email,
                'address' => $validated['address'] ?? $user->address,
                'total_amount' => 0,
                'payment_way' => $validated['payment_method'],
                'status' => 'processing',
            ]);

            // **Set order_number immediately**
            $order->order_number = str_pad($order->id, 6, '0', STR_PAD_LEFT);
            $order->save();

            // Step 2: Add OrderItems
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->price_at_addition,
                ]);
            }

            // Step 3: Recalculate total amount
            $finalTotal = OrderItem::where('order_id', $order->id)
                ->sum(DB::raw('quantity * unit_price'));
            $order->total_amount = $finalTotal;
            $order->save();

              // ✅ Step 3.5: Create Bill (Invoice)
            $bill = Bill::create([
                'user_id'     => $userId,
                'order_id'    => $order->id,
                'bill_number' => 'BILL-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'payment_way' => $validated['payment_method'] === 'paypal' ? 'credit_card' : $validated['payment_method'],
                'total_price' => $finalTotal,
                'status'      => 'not payment', // default
            ]);

            // Step 4: Notify suppliers after order_number & total_amount are set
            $order->load('orderItems.product.supplier.user'); // preload relations

            foreach ($order->orderItems as $orderItem) {
                $product = $orderItem->product;

                if ($product->supplier && $product->supplier->user) {
                    $supplier = $product->supplier->user;
                    $settings = $supplier->notification_settings ?? [];

                    if (
                        ($settings['receive_in_app'] ?? false) &&
                        ($settings['receive_new_order'] ?? false)
                    ) {
                        $supplier->notify(new \App\Notifications\NewOrderNotification($order));
                    }
                }
            }

            // Step 5: Clear the cart
         // بدل الحذف: حدث حالة الكارت إلى "ordered"
$cart->update(['status' => 'ordered']);


            DB::commit();

            return redirect()->route('cart.index', [
                'step' => 3,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إتمام الطلب، يرجى المحاولة مرة أخرى.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
