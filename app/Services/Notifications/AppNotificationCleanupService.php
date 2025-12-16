<?php

namespace App\Services\Notifications;

use App\Models\AppNotification;
use Illuminate\Support\Carbon;

class AppNotificationCleanupService
{
    /**
     * Permanently delete app notifications that were soft-deleted on or before the cutoff date.
     *
     * @return array{deleted: int, cutoff: \Illuminate\Support\Carbon, days: int}
     */
    public function cleanupDeleted(int $days): array
    {
        $days = max(1, (int) $days);
        $cutoff = Carbon::now()->subDays($days);

        $deleted = AppNotification::query()
            ->onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->forceDelete();

        return [
            'deleted' => (int) $deleted,
            'cutoff'  => $cutoff,
            'days'    => $days,
        ];
    }
}
