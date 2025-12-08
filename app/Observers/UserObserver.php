<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\UserAccountRestoreTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    use UserAccountRestoreTrait;

    public function created(User $user): void
    {
        $this->resetUserCache($user);
    }

    public function updated(User $user): void
    {
        $this->resetUserCache($user);
    }

    public function deleted(User $user): void
    {
        $user->update([
            'auto_destroy_date' => $this->generateExpireTimestamp(),
            'restore_date'      => null,
        ]);

        $this->resetUserCache($user);
    }

    public function restored(User $user): void
    {
        $user->update([
            'auto_destroy_date' => null,
            'restore_date'      => Carbon::now(),
        ]);

        $this->resetUserCache($user);
    }

    public function forceDeleted(User $user): void
    {
        $this->resetUserCache($user);
    }

    private function resetUserCache(User $user, string $key = 'user_')
    {
        Cache::forget($key.$user->id);
    }
}
