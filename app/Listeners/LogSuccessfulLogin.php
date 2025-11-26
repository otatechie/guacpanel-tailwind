<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
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
