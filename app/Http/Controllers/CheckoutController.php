<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
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
        $paymentStatus = 'success';

        if ($paymentStatus !== 'success') {
            return response()->json(['success' => false, 'message' => 'فشل في معالجة الدفع.'], 400);
        }

        $user = Auth::user();
        $userId = $user->id;

        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'السلة فارغة، لا يمكن إتمام الطلب.'], 400);
        }

        DB::beginTransaction();

        // try {
            // Step 1: Create a new Order record with a zero total
            $order = Order::create([
                'user_id' => $userId,
                'first_name' => $validated['first_name'] ?? $user->first_name,
                'last_name' => $validated['last_name'] ?? $user->last_name,
                'phone_number' => $validated['phone'] ?? $user->phone, 
                'email' => $validated['email'] ?? $user->email,
                'address' => $validated['address'] ?? $user->address,
                'total_amount' => 0, // Set to 0 temporarily
                'payment_way' => $validated['payment_method'],
                'status' => 'processing', 
            ]);

            // Step 2: Loop through the CartItem records and create the OrderItem records
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->price_at_addition, 
                ]);
            }
            
            // Step 3: Recalculate and update the final total
            $finalTotal = OrderItem::where('order_id', $order->id)->sum(DB::raw('quantity * unit_price'));
            
            // Step 4: Update the order with the final total
            $order->total_amount = $finalTotal;
            $order->save();

            // Delete the Cart and its associated CartItems
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            // Return a JSON response with the order data
            $order->load('orderItems'); // Make sure the orderItems relationship is defined on your Order model
return redirect()->route('cart.index', [
    'step' => 3,
    'order_id' => $order->id
]);


        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json([
        //         'success' => false, 
        //         'message' => 'An error occurred during checkout. Please try again.', 
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
    }
}