<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class UserSelectionNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $url;
    protected $icon;

    public function __construct(string $message, string $url = '#', string $icon = null)
    {
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon ?? asset('images/default_notification_icon.png');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // stored in notifications table
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
        ];
    }
}
