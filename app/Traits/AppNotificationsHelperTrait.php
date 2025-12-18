<?php

namespace App\Traits;

use App\Events\AppNotificationsBulkChanged;
use App\Events\AppNotificationStateChanged;
use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

trait AppNotificationsHelperTrait
{
    protected function resolveNotifications(Request $request, int|string $limit = 25, array $filters = []): array
    {
        $user = $request->user();

        if (!$user) {
            return [
                'data'  => [],
                'links' => [],
                'meta'  => [
                    'total'        => 0,
                    'per_page'     => 0,
                    'current_page' => 1,
                    'last_page'    => 1,
                    'from'         => null,
                    'to'           => null,
                    'total_all'    => 0,
                ],
            ];
        }

        $userId = (string) $user->id;

        $totalAll = $this->countAllNotificationsForUser($userId);

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

        $perPageRaw = $filters['per_page'] ?? $limit;

        if ($perPageRaw === 'all') {
            $limit = max(1, $totalAll);
        } else {
            $perPage = (int) $perPageRaw;
            $limit = $perPage > 0 ? $perPage : $limit;
            $limit = max(1, min($limit, 1000));
        }

        $scope = (string) ($filters['scope'] ?? 'all');
        $read = (string) ($filters['read'] ?? 'all');
        $dismissed = (string) ($filters['dismissed'] ?? 'all');
        $type = (string) ($filters['type'] ?? 'all');
        $search = trim((string) ($filters['search'] ?? ''));
        $sort = (string) ($filters['sort'] ?? 'newest');

        $query = AppNotification::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->from('app_notifications as an')
            ->leftJoin('app_notification_reads as anr', function ($join) use ($userId) {
                $join->on('anr.app_notification_id', '=', 'an.id')
                    ->where('anr.user_id', '=', $userId);
            })
            ->whereNull('an.deleted_at')
            ->where(function ($q) use ($userId) {
                $q->where(function ($q) use ($userId) {
                    $q->where('an.scope', '=', 'user')
                        ->where('an.user_id', '=', $userId)
                        ->whereNull('anr.u_del_notif_at');
                })->orWhere(function ($q) {
                    $q->whereIn('an.scope', ['system', 'release'])
                        ->whereNull('an.user_id')
                        ->whereNull('anr.u_del_notif_at');
                });
            });

        if ($scope !== 'all' && in_array($scope, ['user', 'system', 'release'], true)) {
            if ($scope === 'user') {
                $query->where('an.scope', '=', 'user')
                    ->where('an.user_id', '=', $userId)
                    ->whereNull('anr.u_del_notif_at');
            } else {
                $query->where('an.scope', '=', $scope)
                    ->whereNull('an.user_id')
                    ->whereNull('anr.u_del_notif_at');
            }
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
                        ->whereNull('anr.dismissed_at');
                })->orWhere(function ($q) {
                    $q->whereIn('an.scope', ['system', 'release'])
                        ->whereNull('anr.dismissed_at');
                });
            });
        } elseif ($dismissed === 'dismissed') {
            $query->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('an.scope', '=', 'user')
                        ->whereNotNull('anr.dismissed_at');
                })->orWhere(function ($q) {
                    $q->whereIn('an.scope', ['system', 'release'])
                        ->whereNotNull('anr.dismissed_at');
                });
            });
        }

        if ($read === 'unread') {
            $query->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('an.scope', '=', 'user')
                        ->whereNull('anr.read_at');
                })->orWhere(function ($q) {
                    $q->whereIn('an.scope', ['system', 'release'])
                        ->whereNull('anr.read_at');
                });
            });
        } elseif ($read === 'read') {
            $query->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('an.scope', '=', 'user')
                        ->whereNotNull('anr.read_at');
                })->orWhere(function ($q) {
                    $q->whereIn('an.scope', ['system', 'release'])
                        ->whereNotNull('anr.read_at');
                });
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('an.created_at');
        } else {
            $query->orderByDesc('an.created_at');
        }

        $paginator = $query->paginate($limit, [
            'an.id',
            'an.scope',
            'an.type',
            'an.title',
            'an.message',
            'an.data',
            'an.created_at',
            'anr.read_at as read_at',
            'anr.dismissed_at as dismissed_at',
        ])->withQueryString();

        $paginator->setCollection(
            $paginator->getCollection()->map(function ($row) {
                $readAt = $row->read_at;
                $dismissedAt = $row->dismissed_at;

                return [
                    'id'           => (string) $row->id,
                    'scope'        => $row->scope,
                    'type'         => $row->type,
                    'title'        => $row->title,
                    'message'      => $row->message,
                    'data'         => $row->data,
                    'is_read'      => (bool) $readAt,
                    'read_at'      => $readAt ? Carbon::parse($readAt)->toISOString() : null,
                    'is_dismissed' => (bool) $dismissedAt,
                    'dismissed_at' => $dismissedAt ? Carbon::parse($dismissedAt)->toISOString() : null,
                    'created_at'   => optional($row->created_at)?->toISOString(),
                ];
            }),
        );

        return [
            'data'  => $paginator->items(),
            'links' => [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
            ],
            'meta'  => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
                'total_all'    => $totalAll,
            ],
        ];
    }

    protected function countAllNotificationsForUser(string $userId): int
    {
        return (int) AppNotification::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->from('app_notifications as an')
            ->leftJoin('app_notification_reads as anr', function ($join) use ($userId) {
                $join->on('anr.app_notification_id', '=', 'an.id')
                    ->where('anr.user_id', '=', $userId);
            })
            ->whereNull('an.deleted_at')
            ->where(function ($q) use ($userId) {
                $q->where(function ($q) use ($userId) {
                    $q->where('an.scope', '=', 'user')
                        ->where('an.user_id', '=', $userId)
                        ->whereNull('anr.u_del_notif_at');
                })->orWhere(function ($q) {
                    $q->whereIn('an.scope', ['system', 'release'])
                        ->whereNull('an.user_id')
                        ->whereNull('anr.u_del_notif_at');
                });
            })
            ->count();
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
        $readAt,
        $dismissedAt,
        string $action,
    ): void {
        AppNotificationStateChanged::dispatch(
            $userId,
            $notificationId,
            $scope,
            optional($readAt)?->toISOString(),
            optional($dismissedAt)?->toISOString(),
            $action,
        );
    }

    protected function broadcastBulk(string $userId, string $action, array $ids = []): void
    {
        AppNotificationsBulkChanged::dispatch($userId, $action, $ids);
    }

    protected function markNotificationReadForUser(Request $request, AppNotification $notification): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        if ($notification->scope === 'user') {
            abort_unless((string) $notification->user_id === $userId, 403);
        } else {
            abort_unless(in_array($notification->scope, ['system', 'release'], true) && $notification->user_id === null, 403);
        }

        AppNotificationRead::updateOrCreate(
            [
                'app_notification_id' => (string) $notification->id,
                'user_id'             => $userId,
            ],
            [
                'read_at'       => $now,
                'u_del_notif_at'=> null,
                'deleted_at'    => null,
            ],
        );

        $readRow = AppNotificationRead::query()
            ->where('app_notification_id', (string) $notification->id)
            ->where('user_id', $userId)
            ->first();

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            $notification->scope,
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
        } else {
            abort_unless(in_array($notification->scope, ['system', 'release'], true) && $notification->user_id === null, 403);
        }

        AppNotificationRead::updateOrCreate(
            [
                'app_notification_id' => (string) $notification->id,
                'user_id'             => $userId,
            ],
            [
                'read_at'       => null,
                'u_del_notif_at'=> null,
                'deleted_at'    => null,
            ],
        );

        $readRow = AppNotificationRead::query()
            ->where('app_notification_id', (string) $notification->id)
            ->where('user_id', $userId)
            ->first();

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            $notification->scope,
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

        $ids = AppNotification::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->from('app_notifications as an')
            ->leftJoin('app_notification_reads as anr', function ($join) use ($userId) {
                $join->on('anr.app_notification_id', '=', 'an.id')
                    ->where('anr.user_id', '=', $userId);
            })
            ->whereNull('an.deleted_at')
            ->where(function ($q) use ($userId) {
                $q->where(function ($q) use ($userId) {
                    $q->where('an.scope', '=', 'user')
                        ->where('an.user_id', '=', $userId)
                        ->whereNull('anr.u_del_notif_at');
                })->orWhere(function ($q) {
                    $q->whereIn('an.scope', ['system', 'release'])
                        ->whereNull('an.user_id')
                        ->whereNull('anr.u_del_notif_at');
                });
            })
            ->whereNull('anr.dismissed_at')
            ->whereNull('anr.read_at')
            ->pluck('an.id')
            ->map(fn ($id) => (string) $id);

        if ($ids->isEmpty()) {
            $this->broadcastBulk($userId, 'read-all');

            return;
        }

        $rows = $ids->map(fn ($id) => [
            'app_notification_id' => (string) $id,
            'user_id'             => $userId,
            'read_at'             => $now,
            'u_del_notif_at'      => null,
            'deleted_at'          => null,
            'created_at'          => $now,
            'updated_at'          => $now,
        ])->all();

        AppNotificationRead::upsert(
            $rows,
            ['app_notification_id', 'user_id'],
            ['read_at', 'u_del_notif_at', 'deleted_at', 'updated_at'],
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
        } else {
            abort_unless(in_array($notification->scope, ['system', 'release'], true) && $notification->user_id === null, 403);
        }

        AppNotificationRead::updateOrCreate(
            [
                'app_notification_id' => (string) $notification->id,
                'user_id'             => $userId,
            ],
            [
                'dismissed_at' => $now,
                'deleted_at'   => null,
            ],
        );

        $readRow = AppNotificationRead::query()
            ->where('app_notification_id', (string) $notification->id)
            ->where('user_id', $userId)
            ->first();

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            $notification->scope,
            $readRow?->read_at,
            $readRow?->dismissed_at,
            'dismissed',
        );
    }

    protected function undismissNotificationForUser(Request $request, AppNotification $notification): void
    {
        $user = $request->user();
        $userId = (string) $user->id;

        if ($notification->scope === 'user') {
            abort_unless((string) $notification->user_id === $userId, 403);
        } else {
            abort_unless(in_array($notification->scope, ['system', 'release'], true) && $notification->user_id === null, 403);
        }

        AppNotificationRead::updateOrCreate(
            [
                'app_notification_id' => (string) $notification->id,
                'user_id'             => $userId,
            ],
            [
                'dismissed_at' => null,
                'deleted_at'   => null,
            ],
        );

        $readRow = AppNotificationRead::query()
            ->where('app_notification_id', (string) $notification->id)
            ->where('user_id', $userId)
            ->first();

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            $notification->scope,
            $readRow?->read_at,
            $readRow?->dismissed_at,
            'undismissed',
        );
    }

    protected function dismissAllNotificationsForUser(Request $request): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        $ids = AppNotification::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->from('app_notifications as an')
            ->leftJoin('app_notification_reads as anr', function ($join) use ($userId) {
                $join->on('anr.app_notification_id', '=', 'an.id')
                    ->where('anr.user_id', '=', $userId);
            })
            ->whereNull('an.deleted_at')
            ->where(function ($q) use ($userId) {
                $q->where(function ($q) use ($userId) {
                    $q->where('an.scope', '=', 'user')
                        ->where('an.user_id', '=', $userId)
                        ->whereNull('anr.u_del_notif_at');
                })->orWhere(function ($q) {
                    $q->whereIn('an.scope', ['system', 'release'])
                        ->whereNull('an.user_id')
                        ->whereNull('anr.u_del_notif_at');
                });
            })
            ->whereNull('anr.dismissed_at')
            ->pluck('an.id')
            ->map(fn ($id) => (string) $id);

        if ($ids->isEmpty()) {
            $this->broadcastBulk($userId, 'dismiss-all');

            return;
        }

        $rows = $ids->map(fn ($id) => [
            'app_notification_id' => (string) $id,
            'user_id'             => $userId,
            'dismissed_at'        => $now,
            'u_del_notif_at'      => null,
            'deleted_at'          => null,
            'created_at'          => $now,
            'updated_at'          => $now,
        ])->all();

        AppNotificationRead::upsert(
            $rows,
            ['app_notification_id', 'user_id'],
            ['dismissed_at', 'u_del_notif_at', 'deleted_at', 'updated_at'],
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

        abort_unless(in_array($action, ['read', 'unread', 'dismiss', 'undismiss', 'delete'], true), 422);

        $rows = AppNotification::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->from('app_notifications as an')
            ->leftJoin('app_notification_reads as anr', function ($join) use ($userId) {
                $join->on('anr.app_notification_id', '=', 'an.id')
                    ->where('anr.user_id', '=', $userId);
            })
            ->whereNull('an.deleted_at')
            ->whereIn('an.id', $ids->all())
            ->where(function ($q) use ($userId) {
                $q->where(function ($q) use ($userId) {
                    $q->where('an.scope', '=', 'user')
                        ->where('an.user_id', '=', $userId);
                })->orWhere(function ($q) {
                    $q->whereIn('an.scope', ['system', 'release'])
                        ->whereNull('an.user_id');
                });
            })
            ->get(['an.id', 'an.scope', 'an.user_id']);

        if ($rows->isEmpty()) {
            return;
        }

        $upsertRows = $rows->map(function ($r) use ($action, $now, $userId) {
            $payload = [
                'app_notification_id' => (string) $r->id,
                'user_id'             => $userId,
                'deleted_at'          => null,
                'created_at'          => $now,
                'updated_at'          => $now,
            ];

            if ($action === 'read') {
                $payload['read_at'] = $now;
                $payload['u_del_notif_at'] = null;
            } elseif ($action === 'unread') {
                $payload['read_at'] = null;
                $payload['u_del_notif_at'] = null;
            } elseif ($action === 'dismiss') {
                $payload['dismissed_at'] = $now;
            } elseif ($action === 'undismiss') {
                $payload['dismissed_at'] = null;
            } elseif ($action === 'delete') {
                $payload['u_del_notif_at'] = $now;
            }

            return $payload;
        })->all();

        if ($action === 'read' || $action === 'unread') {
            AppNotificationRead::upsert(
                $upsertRows,
                ['app_notification_id', 'user_id'],
                ['read_at', 'u_del_notif_at', 'deleted_at', 'updated_at'],
            );
        } elseif ($action === 'dismiss' || $action === 'undismiss') {
            AppNotificationRead::upsert(
                $upsertRows,
                ['app_notification_id', 'user_id'],
                ['dismissed_at', 'deleted_at', 'updated_at'],
            );
        } elseif ($action === 'delete') {
            AppNotificationRead::upsert(
                $upsertRows,
                ['app_notification_id', 'user_id'],
                ['u_del_notif_at', 'updated_at'],
            );
        }

        $this->broadcastBulk($userId, 'bulk', $ids->all());
    }

    protected function deleteNotificationForUser(Request $request, AppNotification $notification, bool $canManage = false): void
    {
        $user = $request->user();
        $now = Carbon::now();
        $userId = (string) $user->id;

        if ($notification->scope === 'user') {
            abort_unless((string) $notification->user_id === $userId, 403);

            AppNotificationRead::updateOrCreate(
                [
                    'app_notification_id' => (string) $notification->id,
                    'user_id'             => $userId,
                ],
                [
                    'u_del_notif_at' => $now,
                ],
            );

            $this->broadcastNotificationState(
                $userId,
                (string) $notification->id,
                'user',
                null,
                null,
                'deleted',
            );

            return;
        }

        abort_unless(in_array($notification->scope, ['system', 'release'], true) && $notification->user_id === null, 403);

        if ($canManage) {
            $notificationId = (string) $notification->id;
            $notification->delete();

            $this->broadcastNotificationState(
                $userId,
                $notificationId,
                $notification->scope,
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
                'u_del_notif_at' => $now,
            ],
        );

        $this->broadcastNotificationState(
            $userId,
            (string) $notification->id,
            $notification->scope,
            null,
            null,
            'deleted',
        );
    }
}
