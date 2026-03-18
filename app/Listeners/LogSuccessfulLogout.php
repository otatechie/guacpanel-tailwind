<?php

namespace App\Listeners;

use App\Traits\UserCachingTrait;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    use UserCachingTrait;

    public function handle(Logout $event): void
    {
        if (isset($event->user)) {
            $this->resetUserCache($event->user);
        }
    }
}
