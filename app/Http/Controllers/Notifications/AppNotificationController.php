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

    public function __construct()
    {
        $this->middleware('permission:view-notifications')->only(['index']);

        $this->middleware('permission:edit-notifications')->only([
            'markRead',
            'markUnread',
            'markAllRead',
            'dismiss',
            'undismiss',
            'dismissAll',
            'bulk',
        ]);

        $this->middleware('permission:delete-notifications|manage-notifications')->only(['destroy']);
    }

    public function index(ListNotificationsRequest $request): JsonResponse
    {
        return response()->json($this->resolveNotifications($request, 100));
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
        $canDelete = false;

        $this->deleteNotificationForUser($request, $notification, $canDelete);

        return response()->json(['ok' => true]);
    }
}
