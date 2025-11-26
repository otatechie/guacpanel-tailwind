<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $now = now();
        $user = User::create([
            'name'                => $input['name'],
            'email'               => $input['email'],
            'password'            => Hash::make($input['password']),
            'password_changed_at' => $now,
            'password_expiry_at'  => $now->addMonths(3),
        ]);

        session()->flash('success', 'Great! Your account has been created successfully.');

        return $user;
    }
}
