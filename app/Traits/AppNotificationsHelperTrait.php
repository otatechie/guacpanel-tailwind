<?php

namespace App\Traits;

use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

        if (! $user) {
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

            if (! $notification->read_at) {
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

    protected function markNotificationUnreadForUser(Request $request, AppNotification $notification): void
    {
        $user = $request->user();
        $userId = (string) $user->id;

        if ($notification->scope === 'user') {
            abort_unless((string) $notification->user_id === $userId, 403);

            if ($notification->read_at) {
                $notification->update(['read_at' => null]);
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
                'read_at' => null,
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

            if (! $notification->dismissed_at) {
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

    protected function undismissNotificationForUser(Request $request, AppNotification $notification): void
    {
        $user = $request->user();
        $userId = (string) $user->id;

        if ($notification->scope === 'user') {
            abort_unless((string) $notification->user_id === $userId, 403);

            if ($notification->dismissed_at) {
                $notification->update(['dismissed_at' => null]);
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
                'dismissed_at' => null,
            ],
        );
    }

    protected function dismissAllNotificationsForUser(Request $request): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        AppNotification::query()
            ->where('scope', 'user')
            ->where('user_id', $userId)
            ->whereNull('dismissed_at')
            ->update(['dismissed_at' => $now]);

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

    protected function bulkNotificationsForUser(Request $request): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        $action = (string) $request->input('action');
        $ids = collect((array) $request->input('ids', []))
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return;
        }

        // Split the ids by scope/ownership using a single query
        $rows = AppNotification::query()
            ->whereIn('id', $ids->all())
            ->get(['id', 'scope', 'user_id'])
            ->map(fn ($n) => [
                'id' => (string) $n->id,
                'scope' => $n->scope,
                'user_id' => $n->user_id ? (string) $n->user_id : null,
            ]);

        $userScoped = $rows
            ->filter(fn ($r) => $r['scope'] === 'user' && (string) $r['user_id'] === $userId)
            ->pluck('id')
            ->values();

        $systemScoped = $rows
            ->filter(fn ($r) => $r['scope'] === 'system' && $r['user_id'] === null)
            ->pluck('id')
            ->values();

        if (in_array($action, ['read', 'unread'], true)) {
            if ($userScoped->isNotEmpty()) {
                AppNotification::query()
                    ->whereIn('id', $userScoped->all())
                    ->update(['read_at' => $action === 'read' ? $now : null]);
            }

            if ($systemScoped->isNotEmpty()) {
                $rowsToUpsert = $systemScoped->map(fn ($id) => [
                    'app_notification_id' => (string) $id,
                    'user_id'             => $userId,
                    'read_at'             => $action === 'read' ? $now : null,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ])->all();

                AppNotificationRead::upsert(
                    $rowsToUpsert,
                    ['app_notification_id', 'user_id'],
                    ['read_at', 'updated_at'],
                );
            }

            return;
        }

        if (in_array($action, ['dismiss', 'undismiss'], true)) {
            if ($userScoped->isNotEmpty()) {
                AppNotification::query()
                    ->whereIn('id', $userScoped->all())
                    ->update(['dismissed_at' => $action === 'dismiss' ? $now : null]);
            }

            if ($systemScoped->isNotEmpty()) {
                $rowsToUpsert = $systemScoped->map(fn ($id) => [
                    'app_notification_id' => (string) $id,
                    'user_id'             => $userId,
                    'dismissed_at'        => $action === 'dismiss' ? $now : null,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ])->all();

                AppNotificationRead::upsert(
                    $rowsToUpsert,
                    ['app_notification_id', 'user_id'],
                    ['dismissed_at', 'updated_at'],
                );
            }

            return;
        }

        if ($action === 'delete') {
            // user scope: user can delete their own user-scoped notifications
            if ($userScoped->isNotEmpty()) {
                AppNotification::query()
                    ->whereIn('id', $userScoped->all())
                    ->delete();
            }

            // system scope: "delete" == dismiss for this user (unless manage uses hard delete via destroy())
            if ($systemScoped->isNotEmpty()) {
                $rowsToUpsert = $systemScoped->map(fn ($id) => [
                    'app_notification_id' => (string) $id,
                    'user_id'             => $userId,
                    'dismissed_at'        => $now,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ])->all();

                AppNotificationRead::upsert(
                    $rowsToUpsert,
                    ['app_notification_id', 'user_id'],
                    ['dismissed_at', 'updated_at'],
                );
            }

            return;
        }
    }

    /**
     * Delete semantics:
     * - user scope: hard delete if owned
     * - system scope:
     *   - if manage: hard delete globally
     *   - else: treat as per-user dismiss
     */
    protected function deleteNotificationForUser(Request $request, AppNotification $notification, bool $canManage = false): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        if ($notification->scope === 'user') {
            abort_unless((string) $notification->user_id === $userId, 403);

            $notification->delete();
            return;
        }

        abort_unless($notification->scope === 'system' && $notification->user_id === null, 403);

        if ($canManage) {
            // hard delete globally
            $notification->delete();
            return;
        }

        // normal delete == dismiss for this user
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
}
