<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\BulkNotificationsRequest;
use App\Http\Requests\Notifications\ListNotificationsRequest;
use App\Models\AppNotification;
use App\Traits\AppNotificationsHelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    use AppNotificationsHelperTrait;

    public function index(ListNotificationsRequest $request): JsonResponse
    {
        return response()->json(
            $this->resolveNotifications($request, 25)
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
        $action = $request->validated('action');
        $user = $request->user();

        if (in_array($action, ['read', 'unread', 'dismiss', 'undismiss'], true)) {
            abort_unless($user?->can('manage-notifications') || $user?->can('edit-notifications'), 403);
        }

        if ($action === 'delete') {
            abort_unless($user?->can('manage-notifications') || $user?->can('delete-notifications'), 403);
        }

        $this->bulkNotificationsForUser($request);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, AppNotification $notification): JsonResponse
    {
        $user = $request->user();
        $canManage = (bool) $user?->can('manage-notifications');

        $this->deleteNotificationForUser($request, $notification, $canManage);

        return response()->json(['ok' => true]);
    }
}
