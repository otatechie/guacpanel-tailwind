<?php

namespace App\Listeners;

use App\Mail\WelcomeMailVerified;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailVerified
{
    public function handle(Verified $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new WelcomeMailVerified($user));
    }
}
