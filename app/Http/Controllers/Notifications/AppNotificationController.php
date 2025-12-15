<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\BulkNotificationsRequest;
use App\Http\Requests\Notifications\ExpireNotificationsRequest;
use App\Http\Requests\Notifications\ListNotificationsRequest;
use App\Models\AppNotification;
use App\Traits\AppNotificationsHelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    use AppNotificationsHelperTrait;

    public function __construct()
    {
        $this->middleware('permission:view-notifications|manage-notifications')->only(['index']);

        $this->middleware('permission:edit-notifications|manage-notifications')->only([
            'markRead',
            'markUnread',
            'markAllRead',
            'dismiss',
            'undismiss',
            'dismissAll',
            'bulk',
        ]);

        $this->middleware('permission:delete-notifications|manage-notifications')->only(['destroy']);

        $this->middleware('permission:manage-notifications')->only(['expire']);
    }

    public function expire(ExpireNotificationsRequest $request): JsonResponse
    {
        $ids = collect((array) $request->validated('ids', []))
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values()
            ->all();

        if (!$ids) {
            return response()->json(['ok' => true]);
        }

        AppNotification::query()
            ->whereIn('id', $ids)
            ->update([
                'auto_expire_on' => now(),
                'updated_at'     => now(),
            ]);

        $userId = (string) $request->user()->id;
        $this->broadcastBulk($userId, 'expire', $ids);

        return response()->json(['ok' => true]);
    }

    public function index(ListNotificationsRequest $request): JsonResponse
    {
        return response()->json(
            $this->resolveNotifications($request, 1000, [
                'dismissed' => 'undismissed',
            ])
        );
    }

    public function markRead(Request $request, AppNotification $notification): JsonResponse
    {
        $this->markNotificationReadForUser($request, $notification);

        return response()->json(['ok' => true]);
    }

    public function markUnread(Request $request, AppNotification $notification): JsonResponse
    {
        $this->markNotificationUnreadForUser($request, $notification);

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $this->markAllNotificationsReadForUser($request);

        return response()->json(['ok' => true]);
    }

    public function dismiss(Request $request, AppNotification $notification): JsonResponse
    {
        $this->dismissNotificationForUser($request, $notification);

        return response()->json(['ok' => true]);
    }

    public function undismiss(Request $request, AppNotification $notification): JsonResponse
    {
        $this->undismissNotificationForUser($request, $notification);

        return response()->json(['ok' => true]);
    }

    public function dismissAll(Request $request): JsonResponse
    {
        $this->dismissAllNotificationsForUser($request);

        return response()->json(['ok' => true]);
    }

    public function bulk(BulkNotificationsRequest $request): JsonResponse
    {
        $action = (string) $request->validated('action');
        $user = $request->user();

        if ($action === 'delete') {
            abort_unless($user?->can('delete-notifications') || $user?->can('manage-notifications'), 403);
        } else {
            abort_unless($user?->can('edit-notifications') || $user?->can('manage-notifications'), 403);
        }

        $this->bulkNotificationsForUser($request);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, AppNotification $notification): JsonResponse
    {
        $user = $request->user();
        $canDelete = (bool) ($user?->can('delete-notifications') || $user?->can('manage-notifications'));

        $this->deleteNotificationForUser($request, $notification, $canDelete);

        return response()->json(['ok' => true]);
    }
}
