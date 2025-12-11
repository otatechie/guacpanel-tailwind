<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        if (isset($event->user)) {
            LoginHistory::create([
                'user_type'        => get_class($event->user),
                'user_id'          => $event->user->id,
                'ip_address'       => request()->ip(),
                'user_agent'       => request()->userAgent(),
                'login_successful' => false,
                'login_at'         => now(),
            ]);
        }
    }
}
