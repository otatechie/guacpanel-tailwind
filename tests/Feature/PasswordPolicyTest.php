<?php

/**
 * Password::default() was in use while Password::defaults() was never
 * configured, so the policy was Laravel's bare min:8. These assertions exist so
 * that cannot silently regress -- a bare default() would make them fail.
 */

use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Support\Facades\Validator;

function passwordRules(): array
{
    return (new class {
        use PasswordValidationRules;

        public function rules(): array
        {
            return $this->passwordRules();
        }
    })->rules();
}

function passwordPasses(string $password): bool
{
    return Validator::make(
        ['password' => $password, 'password_confirmation' => $password],
        ['password' => passwordRules()],
    )->passes();
}

test('it rejects passwords under twelve characters', function () {
    expect(passwordPasses('Short1!'))->toBeFalse()->and(passwordPasses('elevenchar!'))->toBeFalse();
});

test('it accepts a password of twelve characters or more', function () {
    expect(passwordPasses('a-perfectly-fine-passphrase'))->toBeTrue();
});

test('it requires the confirmation to match', function () {
    $fails = Validator::make(
        ['password' => 'a-perfectly-fine-passphrase', 'password_confirmation' => 'something-else'],
        ['password' => passwordRules()],
    )->fails();

    expect($fails)->toBeTrue();
});
