<?php

namespace App\Listeners;

use App\Mail\WelcomeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    public function handle(Registered $event): void
    {
        if (config('guacpanel.email_verification_enabled')) {
            return;
        }

        $user = $event->user;

        Mail::to($user->email)->send(new WelcomeMail($user));
    }
}
