<?php

namespace App\Traits;

use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

trait AppNotificationsHelperTrait
{
    /**
     * Canonical notifications payload (used by Inertia hydration AND API endpoint).
     *
     * Rules:
     * - user notifications: dismissed via app_notifications.dismissed_at
     * - system notifications: dismissed per-user via app_notification_reads.dismissed_at
     * - read status:
     *   - user: app_notifications.read_at
     *   - system: app_notification_reads.read_at
     *
     * @return array{data: array<int, array<string, mixed>>}
     */
    protected function resolveNotifications(Request $request, int $limit = 25): array
    {
        $user = $request->user();

        if (!$user) {
            return ['data' => []];
        }

        $limit = max(1, min($limit, 250));
        $userId = (string) $user->id;

        $rows = AppNotification::query()
            ->from('app_notifications as an')
            ->leftJoin('app_notification_reads as anr', function ($join) use ($userId) {
                $join->on('anr.app_notification_id', '=', 'an.id')
                    ->where('anr.user_id', '=', $userId);
            })
            ->where(function ($q) use ($userId) {
                $q->where(function ($q) use ($userId) {
                    $q->where('an.scope', '=', 'user')
                        ->where('an.user_id', '=', $userId)
                        ->whereNull('an.dismissed_at');
                })->orWhere(function ($q) {
                    $q->where('an.scope', '=', 'system')
                        ->whereNull('an.user_id')
                        ->whereNull('anr.dismissed_at');
                });
            })
            ->orderByDesc('an.created_at')
            ->limit($limit)
            ->get([
                'an.id',
                'an.scope',
                'an.type',
                'an.title',
                'an.message',
                'an.data',
                'an.created_at',
                'an.read_at as user_read_at',
                'an.dismissed_at as user_dismissed_at',
                'anr.read_at as system_read_at',
                'anr.dismissed_at as system_dismissed_at',
            ]);

        $data = $rows->map(function ($row) {
            $readAt = $row->scope === 'user'
                ? $row->user_read_at
                : $row->system_read_at;

            return [
                'id'         => (string) $row->id,
                'scope'      => $row->scope,
                'type'       => $row->type,
                'title'      => $row->title,
                'message'    => $row->message,
                'data'       => $row->data,
                'created_at' => optional($row->created_at)?->toISOString(),
                'read_at'    => optional($readAt)?->toISOString(),
                'is_read'    => (bool) $readAt,
            ];
        })->values()->all();

        return ['data' => $data];
    }

    protected function markNotificationReadForUser(Request $request, AppNotification $notification): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        if ($notification->scope === 'user') {
            abort_unless((string) $notification->user_id === $userId, 403);

            if (!$notification->read_at) {
                $notification->update(['read_at' => $now]);
            }

            return;
        }

        abort_unless($notification->scope === 'system' && $notification->user_id === null, 403);

        AppNotificationRead::updateOrCreate(
            [
                'app_notification_id' => (string) $notification->id,
                'user_id'             => $userId,
            ],
            [
                'read_at' => $now,
            ],
        );
    }

    protected function markAllNotificationsReadForUser(Request $request): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        AppNotification::query()
            ->where('scope', 'user')
            ->where('user_id', $userId)
            ->whereNull('dismissed_at')
            ->whereNull('read_at')
            ->update(['read_at' => $now]);

        $systemIds = AppNotification::query()
            ->from('app_notifications as an')
            ->leftJoin('app_notification_reads as anr', function ($join) use ($userId) {
                $join->on('anr.app_notification_id', '=', 'an.id')
                    ->where('anr.user_id', '=', $userId);
            })
            ->where('an.scope', 'system')
            ->whereNull('an.user_id')
            ->whereNull('anr.dismissed_at')
            ->pluck('an.id')
            ->map(fn ($id) => (string) $id);

        if ($systemIds->isEmpty()) {
            return;
        }

        $rows = $systemIds->map(fn ($id) => [
            'app_notification_id' => $id,
            'user_id'             => $userId,
            'read_at'             => $now,
            'created_at'          => $now,
            'updated_at'          => $now,
        ])->all();

        AppNotificationRead::upsert(
            $rows,
            ['app_notification_id', 'user_id'],
            ['read_at', 'updated_at'],
        );
    }

    protected function dismissNotificationForUser(Request $request, AppNotification $notification): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        if ($notification->scope === 'user') {
            abort_unless((string) $notification->user_id === $userId, 403);

            if (!$notification->dismissed_at) {
                $notification->update(['dismissed_at' => $now]);
            }

            return;
        }

        abort_unless($notification->scope === 'system' && $notification->user_id === null, 403);

        AppNotificationRead::updateOrCreate(
            [
                'app_notification_id' => (string) $notification->id,
                'user_id'             => $userId,
            ],
            [
                'dismissed_at' => $now,
            ],
        );
    }

    protected function dismissAllNotificationsForUser(Request $request): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        // Dismiss all user-scoped notifications for this user
        AppNotification::query()
            ->where('scope', 'user')
            ->where('user_id', $userId)
            ->whereNull('dismissed_at')
            ->update(['dismissed_at' => $now]);

        // Dismiss all system-scoped notifications for this user via receipts
        $systemIds = AppNotification::query()
            ->where('scope', 'system')
            ->whereNull('user_id')
            ->pluck('id')
            ->map(fn ($id) => (string) $id);

        if ($systemIds->isEmpty()) {
            return;
        }

        $rows = $systemIds->map(fn ($id) => [
            'app_notification_id' => $id,
            'user_id'             => $userId,
            'dismissed_at'        => $now,
            'created_at'          => $now,
            'updated_at'          => $now,
        ])->all();

        AppNotificationRead::upsert(
            $rows,
            ['app_notification_id', 'user_id'],
            ['dismissed_at', 'updated_at'],
        );
    }
}
