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
    $response = $this->actingAs($this->user)->get(route('user.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('UserAccount/IndexPage')
            ->has('user', fn($user) => $user->has('name')->has('email')->etc()),
    );
});

test('it allows authenticated users to access two factor authentication page', function () {
    $response = $this->actingAs($this->user)->get(route('user.two.factor'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('UserAccount/IndexTwoFactorAuthenticationPage')
            ->has('user')
            ->has('qrCodeSvg')
            ->has('recoveryCodes'),
    );
});

test('it redirects users with expired password to password expired page', function () {
    Setting::first()->update([
        'password_expiry' => true,
    ]);

    $this->user->update([
        'password_expiry_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($this->user)->get(route('user.index'));

    $response->assertRedirect(route('user.password.expired'));
});

test('it redirects users with valid password away from password expired page', function () {
    $this->user->update([
        'password_expiry_at' => now()->addDays(30),
    ]);

    $response = $this->actingAs($this->user)->get(route('user.password.expired'));

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
            '_token' => 'test-token',
            'password' => 'NewPassword123!',
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
            '_token' => 'test-token',
            'password' => 'password123!',
            'password_confirmation' => 'password123!',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token' => 'test-token',
            'password' => 'Password!',
            'password_confirmation' => 'Password!',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token' => 'test-token',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token' => 'test-token',
            'password' => 'Pass1!',
            'password_confirmation' => 'Pass1!',
        ]);
    $response->assertSessionHasErrors('password');

    $response = $this->actingAs($this->user)
        ->withSession(['_token' => 'test-token'])
        ->post(route('user.password.expired.update'), [
            '_token' => 'test-token',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);
    $response->assertSessionHasErrors('password');
});

test('deleting an account requires the current password', function () {
    config(['guacpanel.user.account.delete_enabled' => true]);

    $this->actingAs($this->user)
        ->post(route('user.delete'), ['password' => 'not-the-password'])
        ->assertSessionHasErrors('password');

    expect(User::withTrashed()->find($this->user->id)->trashed())->toBeFalse();
    $this->assertAuthenticatedAs($this->user);
});

test('the correct password deletes the account', function () {
    config(['guacpanel.user.account.delete_enabled' => true]);

    $this->actingAs($this->user)
        ->post(route('user.delete'), ['password' => 'password'])
        ->assertRedirect(route('home'));

    expect(User::withTrashed()->find($this->user->id)->trashed())->toBeTrue();
    $this->assertGuest();
});

test('an account without a password can still be deleted', function () {
    config(['guacpanel.user.account.delete_enabled' => true]);
    $socialUser = User::factory()->create(['password' => null]);

    $this->actingAs($socialUser)->post(route('user.delete'))->assertRedirect(route('home'));

    expect(User::withTrashed()->find($socialUser->id)->trashed())->toBeTrue();
});

test('the account page tells the page how deletion actually behaves', function () {
    config([
        'guacpanel.user.account.restore_enabled' => true,
        'guacpanel.user.account.days_to_restore' => 60,
    ]);

    $this->actingAs($this->user)
        ->get(route('user.index'))
        ->assertInertia(
            fn($page) => $page
                ->where('restoreEnabled', true)
                ->where('daysToRestore', 60)
                ->where('deletePasswordRequired', true)
                ->etc(),
        );
});

test('session platforms are reported by the name the vendor uses now', function () {
    $format = new ReflectionMethod(App\Http\Controllers\User\BrowserSessionController::class, 'formatAgent');
    $controller = new App\Http\Controllers\User\BrowserSessionController();

    $mac = $format->invoke(
        $controller,
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
    );

    expect($mac['platform'])->toBe('macOS');
    expect($format->invoke($controller, '')['platform'])->toBe('Unknown');
});
