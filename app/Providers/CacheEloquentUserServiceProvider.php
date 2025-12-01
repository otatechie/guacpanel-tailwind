<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;

class CacheEloquentUserServiceProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        return cache()->remember('user_'.$identifier, now()->addDay(), function () use ($identifier) {
            return parent::retrieveById($identifier);
        });
    }
}
