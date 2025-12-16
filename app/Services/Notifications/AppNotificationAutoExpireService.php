<?php

namespace App\Services\Notifications;

use App\Models\AppNotification;
use Illuminate\Support\Carbon;

class AppNotificationAutoExpireService
{
    /**
     * Soft delete notifications where auto_expire_on is set and already passed.
     *
     * @return array{soft_deleted: int, cutoff: \Illuminate\Support\Carbon}
     */
    public function softDeleteExpired(?Carbon $now = null): array
    {
        $cutoff = $now ?: Carbon::now();

        $softDeleted = AppNotification::query()
            ->whereNull('deleted_at')
            ->whereNotNull('auto_expire_on')
            ->where('auto_expire_on', '<=', $cutoff)
            ->delete();

        return [
            'soft_deleted' => (int) $softDeleted,
            'cutoff'       => $cutoff,
        ];
    }
}
