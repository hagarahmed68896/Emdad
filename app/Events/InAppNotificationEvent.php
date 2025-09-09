<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InAppNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public array $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId, array $notification)
    {
        $this->userId = $userId;
        $this->notification = $notification;
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Each user has a private channel
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    /**
     * Event name (optional, makes frontend easier).
     */
    public function broadcastAs(): string
    {
        return 'in-app-notification';
    }
}
