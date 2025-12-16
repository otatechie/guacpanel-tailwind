<?php

namespace App\Services\Notifications;

use App\Events\AppNotificationCreated;
use App\Models\AppNotification;
use Illuminate\Support\Carbon;

class AppNotificationScheduledSendService
{
    public function sendDue(bool $dryRun = false): int
    {
        $now = Carbon::now();
        $sent = 0;

        AppNotification::query()
            ->whereNotNull('scheduled_on')
            ->where('scheduled_on', '<=', $now)
            ->where('sent_as_scheduled', false)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($now) {
                $q->whereNull('auto_expire_on')
                  ->orWhere('auto_expire_on', '>', $now);
            })
            ->orderBy('scheduled_on')
            ->chunkById(200, function ($chunk) use (&$sent, $dryRun) {
                foreach ($chunk as $notification) {
                    if ($dryRun) {
                        $sent++;
                        continue;
                    }

                    event(new AppNotificationCreated($notification));

                    $notification->forceFill([
                        'sent_as_scheduled' => true,
                    ])->save();

                    $sent++;
                }
            });

        return $sent;
    }
}
