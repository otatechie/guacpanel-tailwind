<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

trait UserAccountRestoreTrait
{
    private ?Carbon $_expireTimestamp = null;

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
        return Str::ulid();
    }

    public function setExpireTimestamp(): void
    {
        $this->_expireTimestamp = $this->generateExpireTimestamp();
    }

    public function getDaysToRestore(): int
    {
        $days = (int) config('guacpanel.user.account.days_to_restore', 30);

        return $days > 0 ? $days : 30;
    }

    public function generateExpireTimestamp(): Carbon
    {
        return now()->addDays($this->getDaysToRestore());
    }

    public function generateDestroyThresholdTimestamp(): Carbon
    {
        return now()->subDays($this->getDaysToRestore());
    }

    public function calculateAutoDestroyDate(): ?Carbon
    {
        $deletedAt = $this->deleted_at ?? null;

        if (!$deletedAt instanceof Carbon) {
            return null;
        }

        if (!$this->auto_destroy) {
            return null;
        }

        return $deletedAt->copy()->addDays($this->getDaysToRestore());
    }
}
