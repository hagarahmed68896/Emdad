<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    protected $review;

    public function __construct($review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['database']; // important!
    }

public function toDatabase($notifiable)
{
    return [
        'review_id' => $this->review->id,
        'product_id' => $this->review->product_id,
        'rating' => $this->review->rating,
        'comment' => $this->review->comment,
        'reviewer_id' => $this->review->user_id,
        'reviewer_name' => $this->review->user->full_name ?? 'مستخدم',
        'reviewer_avatar' => $this->review->user->profile_picture ?? 'images/default_avatar.png',
        'product_name' => $this->review->product->name ?? 'المنتج',
        'url' => route('products.show', $this->review->product_id), // optional
    ];
}

}
