<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('users.{userId}', function ($user, string $userId) {
    return (string) $user->id === (string) $userId;
});

Broadcast::channel('system', function ($user) {
    // Start permissive so you can test:
    return true;

    // Later you can restrict (e.g. admins only)
    // return $user->hasRole('admin');
});
