<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        if (isset($event->user)) {
            LoginHistory::create([
                'user_type'        => get_class($event->user),
                'user_id'          => $event->user->id,
                'ip_address'       => request()->ip(),
                'user_agent'       => request()->userAgent(),
                'login_successful' => true,
                'login_at'         => now(),
            ]);
        }
    }
}
