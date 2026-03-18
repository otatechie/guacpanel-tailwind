<?php

namespace App\Listeners;

use App\Events\UserRestored;
use App\Mail\RestoredUserMail;
use Illuminate\Support\Facades\Mail;

class SendRestoredEmail
{
    public function handle(UserRestored $event): void
    {
        Mail::to($event->user->email)->queue(new RestoredUserMail($event->user));
    }
}
