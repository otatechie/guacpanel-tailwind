<?php

namespace App\Console\Commands;

use App\Services\Notifications\AppNotificationCleanupService;
use Illuminate\Console\Command;

class CleanupDeletedAppNotificationsCommand extends Command
{
    protected $signature = 'notifications:cleanup-deleted {days? : Days since deleted_at to permanently delete}';

    protected $description = 'Permanently deletes soft-deleted app notifications older than N days.';

    public function handle(AppNotificationCleanupService $cleanup): int
    {
        $days = $this->argument('days');

        if ($days === null || $days === '') {
            $days = (int) config('guacpanel.notifications.auto_cleanup_deleted_days', 30);
        }

        $days = max(1, (int) $days);

        $result = $cleanup->cleanupDeleted($days);

        $this->info('Deleted notifications: '.$result['deleted']);
        $this->info('Cutoff date: '.$result['cutoff']->toDateTimeString().' ('.$result['days'].' days)');

        return self::SUCCESS;
    }
}
