<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

trait UserAccountRestoreTrait
{
    private $_expireTimestamp;

    public function setupAccountRestore(User $user): string
    {
        $this->setExpireTimestamp();
        $token = $this->generateRestoreToken();
        $this->storeUserRestoreToken($user, $token);

        return $this->generateRestoreUrl($token);
    }

    public function storeUserRestoreToken(User $user, string $token): void
    {
        $user->update([
            'restore_token' => $token,
        ]);
    }

    public function generateRestoreUrl(string $token): string
    {
        $route = config('guacpanel.user.account.restore_route');

        return URL::temporarySignedRoute($route, $this->_expireTimestamp, [
            'token' => $token,
        ]);
    }

    public function generateRestoreToken(): string
    {
        return Str::random(64);
    }

    public function setExpireTimestamp(): void
    {
        $this->_expireTimestamp = $this->generateExpireTimestamp();
    }

    public function generateExpireTimestamp()
    {
        return now()->addDays(intval(config('guacpanel.user.account.days_to_restore')));
    }
}
