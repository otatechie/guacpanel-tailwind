<?php

namespace App\Events;

use App\Models\AppNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public AppNotification $notification)
    {
    }

    public function broadcastOn(): Channel
    {
        if ($this->notification->scope === 'system' || $this->notification->scope === 'release') {
            return new PrivateChannel('system');
        }

        return new PrivateChannel('users.'.$this->notification->user_id);
    }

    public function broadcastAs(): string
    {
        return 'app-notification.created';
    }

    public function broadcastWith(): array
    {
        $readAt = null;

        return [
            'id'         => $this->notification->id,
            'user_id'    => $this->notification->user_id,
            'scope'      => $this->notification->scope,
            'type'       => $this->notification->type,
            'title'      => $this->notification->title,
            'message'    => $this->notification->message,
            'data'       => $this->notification->data,
            'read_at'    => $readAt,
            'created_at' => optional($this->notification->created_at)?->toISOString(),
        ];
    }
}
