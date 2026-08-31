<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AppNotification;
use Symfony\Component\HttpFoundation\Response;

class ShareSystemNotifications
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->inertia()) {
            inertia()->share('systemNotifications', $this->bannerNotifications($request));
        }

        return $next($request);
    }

    /**
     * The banner is the loudest surface in the app, so it honours the same mute
     * preferences the feed does -- silencing the system scope there and still
     * being handed a fixed bar at the top of every page was a contradiction.
     */
    private function bannerNotifications(Request $request)
    {
        $preferences = $request->user()?->notificationPreferences() ?? ['muted_scopes' => []];

        if (in_array('system', $preferences['muted_scopes'], true)) {
            return collect();
        }

        return AppNotification::query()
            ->where('scope', 'system')
            ->whereNull('user_id')
            ->where(function ($q) {
                $q->whereNull('scheduled_on')->orWhere('scheduled_on', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('auto_expire_on')->orWhere('auto_expire_on', '>=', now());
            })
            ->whereNull('deleted_at')
            ->latest()
            ->get()
            ->map(
                fn($notification) => [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                ],
            );
    }
}
