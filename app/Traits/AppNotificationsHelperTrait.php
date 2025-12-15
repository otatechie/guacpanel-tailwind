<?php

namespace App\Traits;

use App\Events\AppNotificationsBulkChanged;
use App\Events\AppNotificationStateChanged;
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
     * Supported filters (optional):
     * - scope: all|user|system
     * - read: all|read|unread
     * - dismissed: all|dismissed|undismissed
     * - type: all|info|success|warning|error
     * - search: string
     * - sort: newest|oldest
     * - per_page: int
     *
     * @return array{data: array<int, array<string, mixed>>}
     */
    protected function resolveNotifications(Request $request, int $limit = 25, array $filters = []): array
    {
        $user = $request->user();

        if (!$user) {
            return ['data' => []];
        }

        $userId = (string) $user->id;

        // Always honor query-string inputs (Inertia filters) even if controllers pass partial $filters.
        // Controller-provided $filters win when keys overlap.
        $requestFilters = $request->only([
            'scope',
            'read',
            'dismissed',
            'type',
            'search',
            'sort',
            'per_page',
        ]);

        $filters = array_merge($requestFilters, $filters);

        $perPage = (int) ($filters['per_page'] ?? $limit);
        $limit = max(1, min($perPage > 0 ? $perPage : $limit, 250));

        $scope = (string) ($filters['scope'] ?? 'all');
        $read = (string) ($filters['read'] ?? 'all');
        $dismissed = (string) ($filters['dismissed'] ?? 'all');
        $type = (string) ($filters['type'] ?? 'all');
        $search = trim((string) ($filters['search'] ?? ''));
        $sort = (string) ($filters['sort'] ?? 'newest');

        $query = AppNotification::query()
            ->from('app_notifications as an')
            ->leftJoin('app_notification_reads as anr', function ($join) use ($userId) {
                $join->on('anr.app_notification_id', '=', 'an.id')
                    ->where('anr.user_id', '=', $userId);
            })
            ->where(function ($q) use ($userId) {
                $q->where(function ($q) use ($userId) {
                    $q->where('an.scope', '=', 'user')
                        ->where('an.user_id', '=', $userId);
                })->orWhere(function ($q) {
                    $q->where('an.scope', '=', 'system')
                        ->whereNull('an.user_id');
                });
            });

        if (in_array($scope, ['user', 'system'], true)) {
            $query->where('an.scope', '=', $scope);
        }

        if ($type !== 'all' && in_array($type, ['info', 'success', 'warning', 'error'], true)) {
            $query->where('an.type', '=', $type);
        }

        if ($search !== '') {
            $like = '%'.str_replace('%', '\\%', $search).'%';
            $query->where(function ($q) use ($like) {
                $q->where('an.title', 'like', $like)
                    ->orWhere('an.message', 'like', $like);
            });
        }

        if ($dismissed === 'undismissed') {
            $query->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('an.scope', '=', 'user')
                        ->whereNull('an.dismissed_at');
                })->orWhere(function ($q) {
                    $q->where('an.scope', '=', 'system')
                        ->whereNull('anr.dismissed_at');
                });
            });
        } elseif ($dismissed === 'dismissed') {
            $query->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('an.scope', '=', 'user')
                        ->whereNotNull('an.dismissed_at');
                })->orWhere(function ($q) {
                    $q->where('an.scope', '=', 'system')
                        ->whereNotNull('anr.dismissed_at');
                });
            });
        }

        if ($read === 'unread') {
            $query->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('an.scope', '=', 'user')
                        ->whereNull('an.read_at');
                })->orWhere(function ($q) {
                    $q->where('an.scope', '=', 'system')
                        ->whereNull('anr.read_at');
                });
            });
        } elseif ($read === 'read') {
            $query->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('an.scope', '=', 'user')
                        ->whereNotNull('an.read_at');
                })->orWhere(function ($q) {
                    $q->where('an.scope', '=', 'system')
                        ->whereNotNull('anr.read_at');
                });
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('an.created_at');
        } else {
            $query->orderByDesc('an.created_at');
        }

        $rows = $query
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

            $dismissedAt = $row->scope === 'user'
                ? $row->user_dismissed_at
                : $row->system_dismissed_at;

            return [
                'id'           => (string) $row->id,
                'scope'        => $row->scope,
                'type'         => $row->type,
                'title'        => $row->title,
                'message'      => $row->message,
                'data'         => $row->data,
                'created_at'   => optional($row->created_at)?->toISOString(),
                'read_at'      => optional($readAt)?->toISOString(),
                'dismissed_at' => optional($dismissedAt)?->toISOString(),
                'is_read'      => (bool) $readAt,
                'is_dismissed' => (bool) $dismissedAt,
            ];
        })->values()->all();

        return ['data' => $data];
    }

    protected function setNotificationReadStateForUser(Request $request, AppNotification $notification, bool $isRead): void
    {
        if ($isRead) {
            $this->markNotificationReadForUser($request, $notification);

            return;
        }

        $this->markNotificationUnreadForUser($request, $notification);
    }

    protected function setNotificationDismissedStateForUser(Request $request, AppNotification $notification, bool $isDismissed): void
    {
        if ($isDismissed) {
            $this->dismissNotificationForUser($request, $notification);

            return;
        }

        $this->undismissNotificationForUser($request, $notification);
    }

    protected function bulkActionForUser(Request $request, array $validated): void
    {
        $request->merge([
            'action' => $validated['action'] ?? null,
            'ids'    => $validated['ids'] ?? [],
        ]);

        $this->bulkNotificationsForUser($request);
    }

    protected function broadcastNotificationState(
        string $userId,
        string $notificationId,
        string $scope,
        ?Carbon $readAt,
        ?Carbon $dismissedAt,
        string $action
    ): void {
        event(new AppNotificationStateChanged(
            userId: $userId,
            notificationId: $notificationId,
            scope: $scope,
            readAt: optional($readAt)?->toISOString(),
            dismissedAt: optional($dismissedAt)?->toISOString(),
            action: $action,
        ));
    }

    protected function broadcastBulk(string $userId, string $action, array $ids = []): void
    {
        event(new AppNotificationsBulkChanged(
            userId: $userId,
            action: $action,
            ids: $ids,
        ));
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

            $fresh = $notification->fresh();

            $this->broadcastNotificationState(
                $userId,
                (string) $notification->id,
                'user',
                $fresh?->read_at,
                $fresh?->dismissed_at,
                'read',
            );

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

        $readRow = AppNotificationRead::query()
            ->where('app_notification_id', (string) $notification->id)
            ->where('user_id', $userId)
            ->first();

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            'system',
            $readRow?->read_at,
            $readRow?->dismissed_at,
            'read',
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

            $fresh = $notification->fresh();

            $this->broadcastNotificationState(
                $userId,
                (string) $notification->id,
                'user',
                $fresh?->read_at,
                $fresh?->dismissed_at,
                'unread',
            );

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

        $readRow = AppNotificationRead::query()
            ->where('app_notification_id', (string) $notification->id)
            ->where('user_id', $userId)
            ->first();

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            'system',
            $readRow?->read_at,
            $readRow?->dismissed_at,
            'unread',
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
            $this->broadcastBulk($userId, 'read-all');

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

        $this->broadcastBulk($userId, 'read-all');
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

            $fresh = $notification->fresh();

            $this->broadcastNotificationState(
                $userId,
                (string) $notification->id,
                'user',
                $fresh?->read_at,
                $fresh?->dismissed_at,
                'dismiss',
            );

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

        $readRow = AppNotificationRead::query()
            ->where('app_notification_id', (string) $notification->id)
            ->where('user_id', $userId)
            ->first();

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            'system',
            $readRow?->read_at,
            $readRow?->dismissed_at,
            'dismiss',
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

            $fresh = $notification->fresh();

            $this->broadcastNotificationState(
                $userId,
                (string) $notification->id,
                'user',
                $fresh?->read_at,
                $fresh?->dismissed_at,
                'undismiss',
            );

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

        $readRow = AppNotificationRead::query()
            ->where('app_notification_id', (string) $notification->id)
            ->where('user_id', $userId)
            ->first();

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            'system',
            $readRow?->read_at,
            $readRow?->dismissed_at,
            'undismiss',
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
            $this->broadcastBulk($userId, 'dismiss-all');

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

        $this->broadcastBulk($userId, 'dismiss-all');
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

        $rows = AppNotification::query()
            ->whereIn('id', $ids->all())
            ->get(['id', 'scope', 'user_id'])
            ->map(fn ($n) => [
                'id'      => (string) $n->id,
                'scope'   => $n->scope,
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

            $this->broadcastBulk($userId, 'bulk', $ids->all());

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

            $this->broadcastBulk($userId, 'bulk', $ids->all());

            return;
        }

        if ($action === 'delete') {
            if ($userScoped->isNotEmpty()) {
                AppNotification::query()
                    ->whereIn('id', $userScoped->all())
                    ->delete();
            }

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

            $this->broadcastBulk($userId, 'bulk', $ids->all());

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

            $notificationId = (string) $notification->id;
            $notification->delete();

            $this->broadcastNotificationState(
                $userId,
                $notificationId,
                'user',
                null,
                null,
                'deleted',
            );

            return;
        }

        abort_unless($notification->scope === 'system' && $notification->user_id === null, 403);

        if ($canManage) {
            $notificationId = (string) $notification->id;
            $notification->delete();

            $this->broadcastNotificationState(
                $userId,
                $notificationId,
                'system',
                null,
                null,
                'deleted',
            );

            return;
        }

        AppNotificationRead::updateOrCreate(
            [
                'app_notification_id' => (string) $notification->id,
                'user_id'             => $userId,
            ],
            [
                'dismissed_at' => $now,
            ],
        );

        $readRow = AppNotificationRead::query()
            ->where('app_notification_id', (string) $notification->id)
            ->where('user_id', $userId)
            ->first();

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            'system',
            $readRow?->read_at,
            $readRow?->dismissed_at,
            'dismiss',
        );
    }
}
