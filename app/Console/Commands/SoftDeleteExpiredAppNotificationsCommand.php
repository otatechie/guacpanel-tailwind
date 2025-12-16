<?php

namespace App\Console\Commands;

use App\Services\Notifications\AppNotificationAutoExpireService;
use Illuminate\Console\Command;

class SoftDeleteExpiredAppNotificationsCommand extends Command
{
    protected $signature = 'notifications:soft-delete-expired';

    protected $description = 'Soft deletes app notifications whose auto_expire_on is set and has passed.';

    public function handle(AppNotificationAutoExpireService $service): int
    {
        $result = $service->softDeleteExpired();

        $this->info('Soft-deleted notifications: '.$result['soft_deleted']);
        $this->info('Cutoff (now): '.$result['cutoff']->toDateTimeString());

        return self::SUCCESS;
    }
}
