<?php

use App\Mail\MagicLoginLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    Cache::forever('passwordless_login', true);
});

test('user can view magic link registration page', function () {
    $response = $this->get(route('magic.create'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Auth/RegisterMagicLink'));
});

test('new user can register with magic link', function () {
    Mail::fake();

    $name = $this->faker->name();
    $email = $this->faker->safeEmail();

    $response = $this->post(route('magic.store'), [
        'name'  => $name,
        'email' => $email,
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('users', [
        'name'  => $name,
        'email' => $email,
    ]);

    Mail::assertSent(MagicLoginLink::class, function ($mail) use ($email) {
        return $mail->hasTo($email);
    });
});

test('user cannot register with invalid data', function () {
    $response = $this->post(route('magic.store'), [
        'name'  => '',
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['name', 'email']);
    $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
});

test('user cannot register with existing email', function () {
    $email = $this->faker->safeEmail();
    User::factory()->create(['email' => $email]);

    $response = $this->post(route('magic.store'), [
        'name'  => $this->faker->name(),
        'email' => $email,
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('existing user can request magic login link', function () {
    Mail::fake();
    $user = User::factory()->create();

    $response = $this->post(route('magic.login'), [
        'email' => $user->email,
    ]);

    $response->assertSessionHas('success');

    Mail::assertSent(MagicLoginLink::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('user cannot request magic link for nonexistent email', function () {
    $nonexistentEmail = $this->faker->safeEmail();

    $response = $this->post(route('magic.login'), [
        'email' => $nonexistentEmail,
    ]);

    $response->assertSessionHasErrors(['email']);
    $response->assertSessionHas('error');
});

test('user can authenticate with valid magic link token', function () {
    $user = User::factory()->create();
    $token = Str::random(40);

    Cache::put("magic_link:{$token}", $user->id, now()->addMinutes(10));

    $url = URL::temporarySignedRoute(
        'magic.login.authenticate',
        now()->addMinutes(5),
        ['token' => $token]
    );

    $response = $this->get($url);

    $response->assertRedirect(config('fortify.home'));
    $this->assertAuthenticatedAs($user);
});

test('user cannot authenticate with invalid token', function () {
    $invalidToken = $this->faker->sha1;

    $url = URL::temporarySignedRoute(
        'magic.login.authenticate',
        now()->addMinutes(5),
        ['token' => $invalidToken]
    );

    $response = $this->get($url);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('user cannot authenticate with expired link signature', function () {
    $token = Str::random(40);
    $user = User::factory()->create();
    Cache::put("magic_link:{$token}", $user->id, now()->addMinutes(10));

    // Create URL with expired signature (time in the past)
    $url = URL::temporarySignedRoute(
        'magic.login.authenticate',
        now()->subMinutes(5),
        ['token' => $token]
    );

    $response = $this->get($url);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('passwordless login is disabled when setting is false', function () {
    Cache::forever('passwordless_login', false);

    $response = $this->get(route('magic.create'));
    $response->assertStatus(404);

    $response = $this->post(route('magic.store'), [
        'name'  => $this->faker->name(),
        'email' => $this->faker->safeEmail(),
    ]);
    $response->assertStatus(404);

    $response = $this->post(route('magic.login'), [
        'email' => $this->faker->safeEmail(),
    ]);
    $response->assertStatus(404);

    Cache::forever('passwordless_login', true);
});
