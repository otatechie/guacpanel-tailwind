<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Traits\AppNotificationsHelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    use AppNotificationsHelperTrait;

    public function index(Request $request): JsonResponse
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

    public function dismissAll(Request $request): JsonResponse
    {
        $this->dismissAllNotificationsForUser($request);

        return response()->json(['ok' => true]);
    }
}
