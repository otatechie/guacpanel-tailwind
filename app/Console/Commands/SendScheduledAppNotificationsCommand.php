<?php

namespace App\Console\Commands;

use App\Services\Notifications\AppNotificationScheduledSendService;
use Illuminate\Console\Command;

class SendScheduledAppNotificationsCommand extends Command
{
    protected $signature = 'notifications:send-scheduled {--dry-run : Do not dispatch events or update sent_as_scheduled}';

    protected $description = 'Send scheduled notifications that are due (scheduled_on passed, not sent_as_scheduled, not deleted, not expired).';

    public function handle(AppNotificationScheduledSendService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $count = $service->sendDue($dryRun);

        if ($dryRun) {
            $this->info("Dry run: {$count} scheduled notifications are due.");
            return self::SUCCESS;
        }

        $this->info("Sent {$count} scheduled notifications.");
        return self::SUCCESS;
    }
}
