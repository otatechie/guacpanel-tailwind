<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Traits\AppNotificationsHelperTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppNotificationPageController extends Controller
{
    use AppNotificationsHelperTrait;

    public function index(Request $request): Response
    {
        return Inertia::render('Notifications/NotificationsIndex', [
            'notifications' => fn () => $this->resolveNotifications($request, 100),
        ]);
    }
}
