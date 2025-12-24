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
            $systemNotifications = AppNotification::query()
                ->where('scope', 'system')
                ->whereNull('user_id')
                ->where(function ($q) {
                    $q->whereNull('scheduled_on')
                        ->orWhere('scheduled_on', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('auto_expire_on')
                        ->orWhere('auto_expire_on', '>=', now());
                })
                ->whereNull('deleted_at')
                ->latest()
                ->get()
                ->map(fn($notification) => [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                ]);

            inertia()->share('systemNotifications', $systemNotifications);
        }

        return $next($request);
    }
}
