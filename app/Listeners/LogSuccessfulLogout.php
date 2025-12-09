<?php

namespace App\Listeners;

use App\Traits\UserCachingTrait;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
