<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'location' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string',  Rule::in([
                'Greater Accra',
                'Ashanti',
                'Eastern',
                'Western',
                'Central',
                'Volta',
                'Northern',
                'Upper East',
                'Upper West',
                'Bono',
                'Bono East',
                'Ahafo',
                'Western North',
                'Oti',
                'Savannah',
                'North East'
            ])],
        ])->validate();

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'location' => $input['location'],
                'region' => $input['region'],
            ])->save();

            session()->flash('success', 'Your profile has been updated successfully.');
        }
    }

    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
