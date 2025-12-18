<?php

namespace App\Jobs;

use App\Mail\NotificationsCleanupReport;
use App\Services\Notifications\AppNotificationCleanupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CleanupDeletedAppNotificationsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(AppNotificationCleanupService $cleanup): int
    {
        $days = (int) config('guacpanel.notifications.auto_cleanup_deleted_days', 30);
        $days = max(1, $days);

        $result = $cleanup->cleanupDeleted($days);

        if (config('guacpanel.notifications.auto_clean_send_email')) {
            $to = (string) config('guacpanel.notifications.auto_clean_send_email_to', '');

            if ($to !== '') {
                Mail::to($to)->send(new NotificationsCleanupReport(
                    deleted: (int) $result['deleted'],
                    cutoff: $result['cutoff'],
                    days: (int) $result['days'],
                ));
            }
        }

        return (int) $result['deleted'];
    }
}
