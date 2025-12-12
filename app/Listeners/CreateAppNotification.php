<?php

namespace App\Listeners;

use App\Events\AppNotificationCreated;
use App\Events\AppNotificationRequested;
use App\Models\AppNotification;

class CreateAppNotification
{
    public function handle(AppNotificationRequested $event): void
    {
        $scope = $event->scope === 'system' ? 'system' : 'user';

        $userId = $scope === 'user' ? $event->userId : null;

        if ($scope === 'user' && empty($userId)) {
            return;
        }

        $notification = AppNotification::create([
            'user_id' => $userId,
            'scope' => $scope,
            'type' => $event->type,
            'title' => $event->title,
            'message' => $event->message,
            'data' => $event->data ?: null,
            'read_at' => null,
        ]);

        AppNotificationCreated::dispatch($notification);
    }
}
