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
        $filters = [
            'scope'     => (string) $request->query('scope', 'all'),
            'read'      => (string) $request->query('read', 'all'),
            'dismissed' => (string) $request->query('dismissed', 'all'),
            'type'      => (string) $request->query('type', 'all'),
            'search'    => (string) $request->query('search', ''),
            'sort'      => (string) $request->query('sort', 'newest'),
            'per_page'  => (int) $request->query('per_page', 100),
        ];

        return Inertia::render('Notifications/NotificationsIndex', [
            'filters'       => $filters,
            'notifications' => fn () => $this->resolveNotifications($request, $filters['per_page'], $filters),
        ]);
    }
}
