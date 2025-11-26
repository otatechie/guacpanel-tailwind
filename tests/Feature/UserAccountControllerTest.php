<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Setting::create([
        'password_expiry' => true,
    ]);
});

test('it redirects unauthenticated users to login page', function () {
    $response = $this->get(route('user.index'));
    $response->assertRedirect(route('login'));

    $response = $this->get(route('user.two.factor'));
    $response->assertRedirect(route('login'));

    $response = $this->get(route('user.password.expired'));
    $response->assertRedirect(route('login'));
});

test('it allows authenticated users to access account page', function () {
    $response = $this->actingAs($this->user)
        ->get(route('user.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('UserAccount/IndexPage')
        ->has(
            'user',
            fn ($user) => $user
            ->has('name')
            ->has('email')
            ->has('location')
        )
    );
});

test('it allows authenticated users to access two factor authentication page', function () {
    $response = $this->actingAs($this->user)
        ->get(route('user.two.factor'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
        ->component('UserAccount/IndexTwoFactorAuthenticationPage')
        ->has('user')
        ->has('qrCodeSvg')
        ->has('recoveryCodes')
    );
});

test('it redirects users with expired password to password expired page', function () {
    Setting::first()->update([
        'password_expiry' => true,
    ]);

    $this->user->update([
        'password_expiry_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('user.index'));

    $response->assertRedirect(route('user.password.expired'));
});

test('it redirects users with valid password away from password expired page', function () {
    $this->user->update([
        'password_expiry_at' => now()->addDays(30),
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('user.password.expired'));

    $response->assertRedirect(route('home'));
});

test('it allows users to update expired password with valid data', function () {
    Setting::first()->update([
        'password_expiry' => true,
    ]);

    $this->user->update([
        'password_expiry_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token'                => 'test-token',
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success');

    $this->user->refresh();
    $this->assertTrue(Hash::check('NewPassword123!', $this->user->password));
    $this->assertTrue($this->user->password_expiry_at > now());
    $this->assertEquals(3, round(now()->diffInMonths($this->user->password_expiry_at)));
});

test('it prevents password update with invalid data', function () {
    $this->user->update([
        'password_expiry_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token'                => 'test-token',
            'password'              => 'password123!',
            'password_confirmation' => 'password123!',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token'                => 'test-token',
            'password'              => 'Password!',
            'password_confirmation' => 'Password!',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token'                => 'test-token',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token'                => 'test-token',
            'password'              => 'Pass1!',
            'password_confirmation' => 'Pass1!',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token'                => 'test-token',
            'password'              => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);
    $response->assertSessionHasErrors('password');
});
