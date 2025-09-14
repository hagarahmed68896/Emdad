<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewReviewNotification;
use App\Models\Product;
use App\Models\Order;
use App\Notifications\AdminActionNotification;
use Illuminate\Support\Facades\Notification;



class ReviewController extends Controller
{
public function toggleLike(Review $review)
{
    $user = \Illuminate\Support\Facades\Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $like = $review->likes()->where('user_id', $user->id);

    if ($like->exists()) {
        $like->delete();
        $liked = false;
    } else {
        $review->likes()->create(['user_id' => $user->id]);
        $liked = true;
    }

    // ✅ Always include updated count:
    $count = $review->likes()->count();

    return response()->json([
        'liked' => $liked,
        'count' => $count
    ]);
}


public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'product_id'   => 'nullable|exists:products,id',
            'rating'       => 'required|integer|min:1|max:5',
            'comment'      => 'nullable|string|max:1000',
            'issue_type'   => 'nullable|in:product,order',
            'order_id'   => 'nullable|exists:orders,id',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors'  => $e->errors()
        ], 422);
    }

    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $isComplaint = false;
    if ($request->rating > 0 && $request->rating <= 3) {
        $isComplaint = true;
    }
    if ($request->complaint === 'yes') {
        $isComplaint = true;
    }

    $review = Review::create([
        'user_id'      => $user->id,
        'product_id'   => $request->product_id,
        'rating'       => $request->rating,
        'comment'      => $request->comment,
        'issue_type'   => $request->issue_type,
        'order_id' => $request->order_id,
        'is_complaint' => $isComplaint,
        'review_date'  => now(),
    ]);

    // 🔔 Notifications
    if ($request->filled('product_id')) {
        $product = Product::with('supplier.user')->find($request->product_id);
        if ($product && $product->supplier && $product->supplier->user) {
            $supplier = $product->supplier->user;
            $settings = $supplier->notification_settings ?? [];

            if (($settings['receive_in_app'] ?? true) &&
                ($settings['receive_new_review'] ?? true)) {
                $supplier->notify(new NewReviewNotification($review));
            }
        }
    } elseif ($request->filled('order_number')) {
        $admins = \App\Models\User::where('account_type', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewOrderReviewNotification($review));
        }
        $user->notify(new \App\Notifications\NewOrderReviewNotification($review));
    }

    return response()->json(['success' => true]);
}




public function edit(Review $review)
{
    $this->authorize('update', $review);
    return response()->json([
        'success' => true,
        'review' => $review
    ]);
}


public function update(Request $request, Review $review)
{
    $this->authorize('update', $review);

    $validated = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
    ]);

    $review->update($validated);

    return response()->json(['success' => true]);
}


public function destroy(Review $review)
{
    $this->authorize('delete', $review);
    $review->delete();

    return redirect()->back()->with('success', __('messages.review_deleted'));
}


public function productsByOrder($orderId)
{
    $order = Order::with('orderItems.product')->findOrFail($orderId);

    $products = $order->orderItems->map(function ($item) {
        return [
            'id' => $item->product->id,
            'name' => $item->product->name,
            'image' => $item->product->image ?? asset('images/default-product.png'),
        ];
    });

    return response()->json($products);
}

// app/Http/Controllers/ReviewController.php

public function close(Request $request, Review $review)
{
    $review->status = 'rejected';
    $review->save();

    return response()->json(['success' => true]);
}


public function takeAction(Request $request, Review $review)
{
    $targetUser = $review->product?->supplier?->user;

    switch ($request->input('action')) {
        case 'approved':
            $review->update(['status' => 'approved']);

            if ($targetUser) {
                $targetUser->notify(new AdminActionNotification(
                    'تم توجيه تحذير لك من قبل الإدارة.',
                    $review->rating,
                    $review->comment,
                    $review->issue_type,
                    $review->order_number,
                    optional($review->product)->name
                ));
            }
            break;

            case 'pending':
            $review->update(['status' => 'pending']);

            // Set the user's account status to inactive
            if ($targetUser) {
                $targetUser->update(['status' => 'inactive']); // 👈 Add this line
                // Optional: Notify the user about the temporary suspension
                $targetUser->notify(new AdminActionNotification(
                    'تم إيقاف حسابك مؤقتاً بسبب شكوى.',
                    $review->rating,
                    $review->comment,
                    $review->issue_type,
                    $review->order_number,
                    optional($review->product)->name
                ));
            }
            break;

        case 'rejected':
            $review->update(['status' => 'rejected']);
            break;
    }

    return response()->json(['success' => true]);
}





}
