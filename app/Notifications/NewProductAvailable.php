<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProductAvailable extends Notification
{
    use Queueable;

    /**
     * The product instance.
     *
     * @var mixed
     */
    protected $product;
    protected $supplierImage;

    /**
     * Create a new notification instance.
     *
     * @param mixed $product
    public function __construct($product)
    {
        $this->product = $product;
        $this->supplierImage = isset($product->supplierImage) ? $product->supplierImage : null;
    }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
        public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'لديك إشعار جديد من منتجات المورد: ' . ($this->product->supplier_name ?? ''),
            'product_name' => $this->product->name,
            'product_id' => $this->product->id,
            'url' => route('products.show', $this->product->id), // Link to the product page
            'image' => $this->supplierImage, // Image to display in the notification popup
        ];
    }
}
