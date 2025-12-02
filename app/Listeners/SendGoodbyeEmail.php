<?php

namespace App\Listeners;

use App\Events\UserDeleted;
use App\Mail\GoodbyeUserMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendGoodbyeEmail implements ShouldQueue
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
    public function handle(UserDeleted $event): void
    {
        Mail::to($event->user->email)->queue(new GoodbyeUserMail($event->user));
    }
}
