<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Offer;

class NewOfferNotification extends Notification
{
    use Queueable;

    protected $offer;

    /**
     * Create a new notification instance.
     *
     * @param Offer $offer
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // In-app notifications
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'offer_id' => $this->offer->id,
            'product_id' => $this->offer->product_id,
            'product_name' => $this->offer->product->name ?? 'المنتج',
            'offer_name' => $this->offer->name,
            'discount_percent' => $this->offer->discount_percent,
            'offer_start' => $this->offer->offer_start->format('Y-m-d'),
            'offer_end' => $this->offer->offer_end->format('Y-m-d'),
            'product_image' => $this->offer->product->image ?? null,
            'message' => "تم إضافة عرض جديد على المنتج " . ($this->offer->product->name ?? 'المنتج') . " بخصم " . $this->offer->discount_percent . "%",
            'url' => route('products.show', ['slug' => $this->offer->product->slug]),
        ];
    }
}
