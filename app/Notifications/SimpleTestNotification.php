<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SimpleTestNotification extends Notification
{
    use Queueable;

    public function __construct(protected string $messageContent = 'This is a test notification.')
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database']; // This tells Laravel to save it to the database
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->messageContent,
            'url' => '/dashboard', // Example URL
            'image' => asset('images/default_avatar.png'), // Example image
        ];
    }
}