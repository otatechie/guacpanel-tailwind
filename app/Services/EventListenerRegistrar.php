<?php

namespace App\Services;

use App\Events\UserDeleted;
use App\Listeners\LogFailedLogin;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\SendGoodbyeEmail;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\SendWelcomeEmailVerified;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Events\Dispatcher;

class EventListenerRegistrar
{
    public function __construct(private Dispatcher $events)
    {
        //
    }

    public function register(): void
    {
        $this->events->listen(
            Registered::class,
            SendWelcomeEmail::class,
        );

        $this->events->listen(
            Verified::class,
            SendWelcomeEmailVerified::class,
        );

        $this->events->listen(
            Failed::class,
            LogFailedLogin::class,
        );

        $this->events->listen(
            Login::class,
            LogSuccessfulLogin::class,
        );

        $this->events->listen(
            Logout::class,
            LogSuccessfulLogout::class,
        );

        $this->events->listen(
            UserDeleted::class,
            SendGoodbyeEmail::class,
        );
    }
}
