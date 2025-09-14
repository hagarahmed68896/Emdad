<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminActionNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $rating;
    protected $comment;
    protected $issueType;
    protected $orderNumber;
    protected $productName;

    /**
     * Create a new notification instance.
     */
    public function __construct($message, $rating = null, $comment = null, $issueType = null, $orderNumber = null, $productName = null)
    {
        $this->message = $message;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->issueType = $issueType;
        $this->orderNumber = $orderNumber;
        $this->productName = $productName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database']; // You can also add 'mail', 'broadcast' if needed
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'issue_type' => $this->issueType,
            'order_number' => $this->orderNumber,
            'product_name' => $this->productName,
        ];
    }
}
