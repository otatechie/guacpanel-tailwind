<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AppNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = AppNotification::query()
            ->where(function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('scope', 'user')
                        ->where('user_id', (string) $user->id);
                })->orWhere(function ($q) {
                    $q->where('scope', 'system')
                        ->whereNull('user_id');
                });
            })
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        $readMap = AppNotificationRead::query()
            ->where('user_id', (string) $user->id)
            ->whereIn('app_notification_id', $notifications->pluck('id'))
            ->get()
            ->keyBy('app_notification_id');

        $data = $notifications->map(function (AppNotification $n) use ($readMap) {
            $readAt = $n->scope === 'user'
                ? $n->read_at
                : $readMap->get($n->id)?->read_at;

            return [
                'id'         => $n->id,
                'scope'      => $n->scope,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'data'       => $n->data,
                'created_at' => optional($n->created_at)?->toISOString(),
                'read_at'    => optional($readAt)?->toISOString(),
                'is_read'    => (bool) $readAt,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function markRead(Request $request, AppNotification $notification): JsonResponse
    {
        $user = $request->user();
        $now = Carbon::now();

        if ($notification->scope === 'user') {
            abort_unless((string) $notification->user_id === (string) $user->id, 403);

            if (!$notification->read_at) {
                $notification->update(['read_at' => $now]);
            }

            return response()->json(['ok' => true]);
        }

        abort_unless($notification->scope === 'system' && $notification->user_id === null, 403);

        AppNotificationRead::updateOrCreate(
            [
                'app_notification_id' => (string) $notification->id,
                'user_id'             => (string) $user->id,
            ],
            ['read_at' => $now],
        );

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $now = Carbon::now();

        AppNotification::query()
            ->where('scope', 'user')
            ->where('user_id', (string) $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => $now]);

        $systemIds = AppNotification::query()
            ->where('scope', 'system')
            ->whereNull('user_id')
            ->pluck('id');

        if ($systemIds->isNotEmpty()) {
            $rows = $systemIds->map(fn ($id) => [
                'app_notification_id' => (string) $id,
                'user_id'             => (string) $user->id,
                'read_at'             => $now,
                'created_at'          => $now,
                'updated_at'          => $now,
            ])->all();

            // bulk upsert instead of foreach updateOrCreate
            AppNotificationRead::upsert(
                $rows,
                ['app_notification_id', 'user_id'],
                ['read_at', 'updated_at'],
            );
        }

        return response()->json(['ok' => true]);
    }
}
