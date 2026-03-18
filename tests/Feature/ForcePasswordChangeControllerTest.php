<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'force_password_change' => true,
    ]);
});

test('it redirects unauthenticated users to login page', function () {
    $response = $this->get(route('user.password.change'));
    $response->assertRedirect(route('login'));
});

test('it redirects users without force password change to home page', function () {
    $user = User::factory()->create(['force_password_change' => false]);

    $response = $this->actingAs($user)->get(route('user.password.change'));
    $response->assertRedirect(route('home'));
});

test('it allows access to users with force password change required', function () {
    $response = $this->actingAs($this->user)->get(route('user.password.change'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Auth/ChangePassword')
            ->has('user')
    );
});

test('it allows users to update password with valid data', function () {
    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.change.update'), [
            '_token'                => 'test-token',
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success');

    $this->user->refresh();
    $this->assertTrue(Hash::check('NewPassword123!', $this->user->password));
    $this->assertFalse($this->user->force_password_change);
});

test('it prevents password update with invalid data', function () {
    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.change.update'), [
            '_token'                => 'test-token',
            'password'              => 'password123!',
            'password_confirmation' => 'password123!',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.change.update'), [
            '_token'                => 'test-token',
            'password'              => 'Password!',
            'password_confirmation' => 'Password!',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.change.update'), [
            '_token'                => 'test-token',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.change.update'), [
            '_token'                => 'test-token',
            'password'              => 'Pass1!',
            'password_confirmation' => 'Pass1!',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.change.update'), [
            '_token'                => 'test-token',
            'password'              => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);
    $response->assertSessionHasErrors('password');
});

test('it enforces rate limiting for password update attempts', function () {
    $key = 'user.password.change.update:'.$this->user->id;

    for ($i = 0; $i < 3; $i++) {
        $response = $this->actingAs($this->user)
            ->withSession(['_token' => 'test-token'])
            ->post(route('user.password.change.update'), [
                '_token'                => 'test-token',
                'password'              => 'short',
                'password_confirmation' => 'short',
            ]);
    }

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.change.update'), [
            '_token'                => 'test-token',
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

    $response->assertSessionHasErrors('password');
    $this->assertStringContainsString('Too many attempts', session('errors')->first('password'));

    RateLimiter::clear($key);
    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.change.update'), [
            '_token'                => 'test-token',
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success');
});
