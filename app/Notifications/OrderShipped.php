<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage; 

class OrderShipped extends Notification implements ShouldQueue 
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Specify 'database' channel
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Your order #' . $this->order->id . ' has been shipped!',
            'url' => route('orders.show', $this->order->id), // Link to the order details page
        ];
    }


    /**
     * Get the notification's delivery channels.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Your order #' . $this->order->id . ' has been shipped!',
            'url' => route('orders.show', $this->order->id), // Link to the order details page
        ];
    }
}