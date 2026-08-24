<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create permission
    Permission::firstOrCreate(['name' => 'manage-settings']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('manage-settings');

    $this->regularUser = User::factory()->create();

    Setting::updateOrCreate(
        [],
        [
            'password_expiry' => false,
            'passwordless_login' => false,
            'two_factor_authentication' => false,
        ],
    );

    $this->testToken = 'test-token';
});

test('it allows admin to access settings index page', function () {
    $response = $this->actingAs($this->adminUser)->get(route('admin.setting.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn($page) => $page->component('Admin/IndexSettingPage'));
});

test('it allows admin to access settings management page', function () {
    $response = $this->actingAs($this->adminUser)->get(route('admin.setting.show'));

    $response->assertStatus(200);
    $response->assertInertia(fn($page) => $page->component('Admin/IndexManageSettingPage')->has('systemSettings'));
});

test('it allows admin to update settings', function () {
    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.setting.update'), [
            '_token' => $this->testToken,
            'password_expiry' => true,
            'passwordless_login' => true,
            'two_factor_authentication' => true,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('settings', [
        'password_expiry' => true,
        'passwordless_login' => true,
        'two_factor_authentication' => true,
    ]);
});

test('it allows admin to toggle settings individually', function () {
    // Update password_expiry only
    $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.setting.update'), [
            '_token' => $this->testToken,
            'password_expiry' => true,
            'passwordless_login' => false,
            'two_factor_authentication' => false,
        ]);

    $this->assertDatabaseHas('settings', [
        'password_expiry' => true,
        'passwordless_login' => false,
        'two_factor_authentication' => false,
    ]);

    // Update two_factor_authentication only
    $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.setting.update'), [
            '_token' => $this->testToken,
            'password_expiry' => false,
            'passwordless_login' => false,
            'two_factor_authentication' => true,
        ]);

    $this->assertDatabaseHas('settings', [
        'password_expiry' => false,
        'passwordless_login' => false,
        'two_factor_authentication' => true,
    ]);
});

test('it denies access to users without permission', function () {
    $response = $this->actingAs($this->regularUser)->get(route('admin.setting.index'));

    $response->assertForbidden();

    $response = $this->actingAs($this->regularUser)->get(route('admin.setting.show'));

    $response->assertForbidden();
});

test('it denies settings update to users without permission', function () {
    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.setting.update'), [
            '_token' => $this->testToken,
            'password_expiry' => true,
        ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('settings', [
        'password_expiry' => false,
    ]);
});

test('it creates settings if none exist', function () {
    Setting::query()->delete();
    $this->assertDatabaseMissing('settings', []);

    $response = $this->actingAs($this->adminUser)->get(route('admin.setting.show'));

    $response->assertStatus(200);

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.setting.update'), [
            '_token' => $this->testToken,
            'password_expiry' => true,
            'passwordless_login' => true,
            'two_factor_authentication' => true,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('settings', [
        'password_expiry' => true,
        'passwordless_login' => true,
        'two_factor_authentication' => true,
    ]);
});

test('it reports who last changed the settings', function () {
    // Auditing is off in console context by default, and Pest is console context.
    config(['audit.console' => true]);

    // beforeEach seeded the row outside the request cycle; start from nothing so
    // the only audit is the one the update writes.
    Setting::query()->delete();

    $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.setting.update'), [
            '_token' => $this->testToken,
            'password_expiry' => true,
            'passwordless_login' => false,
            'two_factor_authentication' => false,
        ]);

    $response = $this->actingAs($this->adminUser)->get(route('admin.setting.show'));

    $response->assertInertia(
        fn($page) => $page->where('lastChanged.by', $this->adminUser->name)->has('lastChanged.at'),
    );
});
