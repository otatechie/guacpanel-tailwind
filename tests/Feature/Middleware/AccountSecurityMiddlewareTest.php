<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

// EnsureAccountNotLocked

test('locked account is logged out and redirected to login', function () {
    $user = User::factory()->create(['account_locked' => true]);

    $response = $this->actingAs($user)->get(route('user.index'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});

test('unlocked account passes the lock check', function () {
    $user = User::factory()->create(['account_locked' => false]);

    $response = $this->actingAs($user)->get(route('user.index'));

    $response->assertStatus(200);
    $this->assertAuthenticatedAs($user);
});

// DisableAccount

test('disabled account is logged out and redirected to login', function () {
    $user = User::factory()->create(['disable_account' => true]);

    $response = $this->actingAs($user)->get(route('user.index'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('warning');
    $this->assertGuest();
});

// ForcePasswordChange

test('user flagged for forced password change is redirected to the change page', function () {
    $user = User::factory()->create(['force_password_change' => true]);

    $response = $this->actingAs($user)->get(route('user.index'));

    $response->assertRedirect(route('user.password.change'));
});

test('forced password change does not block the change page itself', function () {
    $user = User::factory()->create(['force_password_change' => true]);

    $response = $this->actingAs($user)->get(route('user.password.change'));

    $response->assertStatus(200);
});

// CheckPasswordExpiry

test('user with expired password is redirected to the password expired page', function () {
    Setting::create(['password_expiry' => true]);
    $user = User::factory()->create(['password_expiry_at' => now()->subDay()]);

    $response = $this->actingAs($user)->get(route('user.index'));

    $response->assertRedirect(route('user.password.expired'));
});

test('password expiry is not enforced when the setting is off', function () {
    Setting::create(['password_expiry' => false]);
    $user = User::factory()->create(['password_expiry_at' => now()->subDay()]);

    $response = $this->actingAs($user)->get(route('user.index'));

    $response->assertStatus(200);
});

// RequireTwoFactor

test('user without 2FA is redirected to 2FA setup when enforcement is on', function () {
    Setting::create(['two_factor_authentication' => true]);
    Permission::firstOrCreate(['name' => 'manage-settings']);

    $user = User::factory()->create();
    $user->givePermissionTo('manage-settings');

    $response = $this->actingAs($user)->get(route('admin.setting.index'));

    $response->assertRedirect(route('user.two.factor'));
});

test('user with 2FA configured passes enforcement', function () {
    Setting::create(['two_factor_authentication' => true]);
    Permission::firstOrCreate(['name' => 'manage-settings']);

    $user = User::factory()->create(['two_factor_secret' => encrypt('secret')]);
    $user->givePermissionTo('manage-settings');

    $response = $this->actingAs($user)->get(route('admin.setting.index'));

    $response->assertStatus(200);
});

test('2FA is not enforced when the setting is off', function () {
    Setting::create(['two_factor_authentication' => false]);
    Permission::firstOrCreate(['name' => 'manage-settings']);

    $user = User::factory()->create();
    $user->givePermissionTo('manage-settings');

    $response = $this->actingAs($user)->get(route('admin.setting.index'));

    $response->assertStatus(200);
});
