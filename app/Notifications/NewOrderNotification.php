<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public $order;

   public function __construct($order)
{
    // Reload order with user relation
    $this->order = $order->fresh(['user', 'orderItems']);
}


    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id'      => $this->order->id,
            'order_number' => str_pad($this->order->id, 6, '0', STR_PAD_LEFT),
            'order_total'   => $this->order->total_amount ?? 0,
            'customer_name' => $this->order->user->full_name ?? 'مستخدم',
            'message'       => 'لديك طلب جديد من المستخدم ' . ($this->order->user->full_name ?? 'مستخدم'),
        ];
    }
}
