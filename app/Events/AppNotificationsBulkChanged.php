<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppNotificationsBulkChanged implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public string $userId,
        public string $action,
        public array $ids = [],
    ) {
    }

    public function broadcastOn(): Channel|array
    {
        if ($this->userId === 'system') {
            return new PrivateChannel('system');
        }

        return new PrivateChannel("users.{$this->userId}");
    }

    public function broadcastAs(): string
    {
        return 'app-notification.bulk';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'ids'    => $this->ids,
        ];
    }
}
