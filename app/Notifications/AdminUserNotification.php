<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
protected $messageContent;
    protected $category;
    protected $notification_type;
    protected $status;

 public function __construct($title, $messageContent, $category, $notification_type, $status)
{
    $this->title             = $title;
    $this->messageContent    = $messageContent;
    $this->category          = $category;
    $this->notification_type = $notification_type;
    $this->status            = $status;
}

    public function via($notifiable)
    {
        return ['database'];
    }

  
public function toDatabase($notifiable)
{
    return [
        'title'             => $this->title,
        'category'          => $this->category,
        'notification_type' => $this->notification_type,
        'status'            => $this->status,
        'content'           => $this->messageContent, // still stored as "content"
    ];
}
}


