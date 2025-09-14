<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Review;

class NewOrderReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type'        => 'order_review',
            'order_number'=> $this->review->order_number,
            'rating'      => $this->review->rating,
            'comment'     => $this->review->comment,
            'issue_type'  => $this->review->issue_type,
            'user'        => $this->review->user->only(['id','name','email']),
        ];
    }
}
