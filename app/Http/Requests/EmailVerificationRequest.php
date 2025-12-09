<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest as BaseEmailVerificationRequest;

class EmailVerificationRequest extends BaseEmailVerificationRequest
{
    protected function prepareForValidation(): void
    {
        $user = User::findOrFail($this->route('id'));

        $this->setUserResolver(function () use ($user) {
            return $user;
        });

    }
}
