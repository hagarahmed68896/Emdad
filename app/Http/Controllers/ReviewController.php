<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewReviewNotification;
use App\Models\Product;


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
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $review = \App\Models\Review::create([
        'product_id' => $request->product_id,
        'user_id' => $user->id,
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    $product = \App\Models\Product::with('supplier.user')->findOrFail($request->product_id);

    if ($product->supplier && $product->supplier->user) {
        $supplier = $product->supplier->user;

        $settings = $supplier->notification_settings ?? [];

        if (($settings['receive_in_app'] ?? true) &&
            ($settings['receive_new_review'] ?? true)) {
            $supplier->notify(new \App\Notifications\NewReviewNotification($review));
        }
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



}
