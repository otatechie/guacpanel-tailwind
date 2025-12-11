<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

trait UserCachingTrait
{
    protected function resetUserCache(User $user, string $key = 'user_'): void
    {
        Cache::forget($key.$user->id);
    }
}
