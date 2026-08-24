<?php

use App\Mail\ExceptionOccurred;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('exception emails are disabled by default', function () {
    // Guard against the config default regressing to true: a hand-configured
    // production env without EMAIL_EXCEPTION_ENABLED must not mail request bodies.
    expect(config('exceptions.emailExceptionEnabled'))->toBeFalse();
})->skip(env('EMAIL_EXCEPTION_ENABLED') !== null, 'EMAIL_EXCEPTION_ENABLED is set in this environment');

test('exception emails redact passwords and secrets from the request body', function () {
    config([
        'exceptions.emailExceptionEnabled' => true,
        'exceptions.emailExceptionsTo' => 'admin@example.com',
        'exceptions.emailExceptionFrom' => 'noreply@example.com',
    ]);

    Mail::fake();

    Route::post('/_boom', function () {
        throw new RuntimeException('boom');
    })->middleware('web');

    $this->withSession(['_token' => 'test-token'])->post('/_boom', [
        '_token' => 'test-token',
        'email' => 'victim@example.com',
        'password' => 'super-secret-password',
        'password_confirmation' => 'super-secret-password',
        'two_factor_code' => '123456',
    ]);

    Mail::assertSent(ExceptionOccurred::class, function (ExceptionOccurred $mail) {
        $property = (new ReflectionClass($mail))->getProperty('content');
        $content = $property->getValue($mail);

        return $content['body']['password'] === '[REDACTED]' &&
            $content['body']['password_confirmation'] === '[REDACTED]' &&
            $content['body']['two_factor_code'] === '[REDACTED]' &&
            $content['body']['email'] === 'victim@example.com';
    });
});
