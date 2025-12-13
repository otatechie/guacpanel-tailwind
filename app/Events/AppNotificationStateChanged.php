<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppNotificationStateChanged implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public string $userId,
        public string $notificationId,
        public string $scope,                 // user|system
        public ?string $readAt = null,        // ISO string or null
        public ?string $dismissedAt = null,   // ISO string or null
        public string $action = 'updated',    // read|unread|dismiss|undismiss|deleted|updated
    ) {
    }

    public function broadcastOn(): Channel|array
    {
        return new PrivateChannel('users.'.$this->userId);
    }

    public function broadcastAs(): string
    {
        return 'app-notification.state';
    }

    public function broadcastWith(): array
    {
        return [
            'id'           => $this->notificationId,
            'scope'        => $this->scope,
            'read_at'      => $this->readAt,
            'dismissed_at' => $this->dismissedAt,
            'action'       => $this->action,
        ];
    }
}
