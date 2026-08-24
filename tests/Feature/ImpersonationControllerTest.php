<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'impersonate-users']);
    Role::firstOrCreate(['name' => 'superuser']);

    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo('impersonate-users');

    $this->target = User::factory()->create();

    $this->testToken = 'test-token';
});

test('users without the permission cannot impersonate', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.user.impersonate.start', $this->target), ['_token' => $this->testToken]);

    $response->assertForbidden();
    $this->assertAuthenticatedAs($user);
});

test('permitted users can impersonate another user', function () {
    $response = $this->actingAs($this->admin)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.user.impersonate.start', $this->target), ['_token' => $this->testToken]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($this->target);
    expect(session('impersonator_id'))->toBe($this->admin->id);
});

test('users cannot impersonate themselves', function () {
    $response = $this->actingAs($this->admin)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.user.impersonate.start', $this->admin), ['_token' => $this->testToken]);

    $response->assertSessionHas('error');
    $this->assertAuthenticatedAs($this->admin);
});

test('non-superusers cannot impersonate a superuser', function () {
    $this->target->assignRole('superuser');

    $response = $this->actingAs($this->admin)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.user.impersonate.start', $this->target), ['_token' => $this->testToken]);

    $response->assertSessionHas('error');
    $this->assertAuthenticatedAs($this->admin);
    expect(session('impersonator_id'))->toBeNull();
});

test('stopping impersonation restores the original user', function () {
    $this->actingAs($this->admin)
        ->withSession(['_token' => $this->testToken])
        ->post(route('admin.user.impersonate.start', $this->target), ['_token' => $this->testToken]);

    $this->assertAuthenticatedAs($this->target);

    $response = $this->withSession(['_token' => $this->testToken])->post(route('admin.user.impersonate.stop'), [
        '_token' => $this->testToken,
    ]);

    $response->assertRedirect(route('admin.user.index'));
    $this->assertAuthenticatedAs($this->admin);
    expect(session('impersonator_id'))->toBeNull();
});
