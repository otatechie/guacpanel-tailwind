<?php

namespace App\Observers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user): void
    {
        if (! $user->user_slug) {
            $user->user_slug = 'user-' . Str::ulid();
        }

        if (! $user->password) {
            $user->password = null;
        }
    }

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
            'restore_date' => null,
        ]);

        $this->resetUserCache($user);
    }

    public function restored(User $user): void
    {
        $user->update([
            'restore_date' => Carbon::now(),
        ]);

        $this->resetUserCache($user);
    }

    public function forceDeleted(User $user): void
    {
        $this->resetUserCache($user);
    }

    private function resetUserCache(User $user, string $key = 'user_'): void
    {
        Cache::forget($key . $user->id);
    }
}
