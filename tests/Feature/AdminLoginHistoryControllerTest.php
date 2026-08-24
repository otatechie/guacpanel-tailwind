<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view-login-history']);
    Permission::firstOrCreate(['name' => 'manage-login-history']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('view-login-history');

    $this->manageUser = User::factory()->create();
    $this->manageUser->givePermissionTo('manage-login-history');

    $this->regularUser = User::factory()->create();
});

test('it redirects unauthenticated users to login page', function () {
    $this->get(route('admin.login.history.index'))->assertRedirect(route('login'));
});

test('it denies access to users without login history permission', function () {
    $this->actingAs($this->regularUser)->get(route('admin.login.history.index'))->assertForbidden();
});

test('it allows access to users with login history permission', function () {
    $this->actingAs($this->adminUser)
        ->get(route('admin.login.history.index'))
        ->assertStatus(200)
        ->assertInertia(fn(Assert $page) => $page->component('Admin/IndexLoginHistoryPage')->has('loginHistory'));
});

test('view permission cannot bulk-delete login history', function () {
    $record = $this->regularUser->loginHistory()->create(['login_at' => now()]);

    $this->actingAs($this->adminUser)
        ->withSession(['_token' => 'test-token'])
        ->post(route('admin.login.history.bulk-destroy'), [
            '_token' => 'test-token',
            'ids' => [$record->id],
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('login_history', ['id' => $record->id]);
});

test('manage permission can bulk-delete login history', function () {
    $record = $this->regularUser->loginHistory()->create(['login_at' => now()]);

    $this->actingAs($this->manageUser)
        ->withSession(['_token' => 'test-token'])
        ->post(route('admin.login.history.bulk-destroy'), [
            '_token' => 'test-token',
            'ids' => [$record->id],
        ])
        ->assertRedirect(route('admin.login.history.index'));

    $this->assertDatabaseMissing('login_history', ['id' => $record->id]);
});
