<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create required permissions
    Permission::firstOrCreate(['name' => 'manage roles']);

    // Create test roles
    $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
    $this->editorRole = Role::firstOrCreate(['name' => 'editor']);

    // Create users with different permission levels
    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('manage roles');

    $this->regularUser = User::factory()->create();
});

test('unauthenticated user cannot access role management', function () {
    $response = $this->get(route('admin.role.index'));
    $response->assertRedirect(route('login'));
});

test('user without role management permission cannot access role management', function () {
    $response = $this->actingAs($this->regularUser)
        ->get(route('admin.role.index'));
    $response->assertForbidden();
});

test('user with role management permission can create roles', function () {
    $roleData = [
        'name' => 'test-role',
        'permissions' => [],
        '_token' => 'test-token'
    ];

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => 'test-token'])
        ->post(route('admin.role.store'), $roleData);

    $response->assertRedirect();
    $this->assertDatabaseHas('roles', ['name' => 'test-role']);
});

test('user with role management permission can update roles', function () {
    $role = Role::create(['name' => 'test-role']);
    $updateData = [
        'name' => 'updated-role',
        'permissions' => [],
        '_token' => 'test-token'
    ];

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => 'test-token'])
        ->put(route('admin.role.update', $role), $updateData);

    $response->assertRedirect();
    $this->assertDatabaseHas('roles', ['name' => 'updated-role']);
});

test('user with role management permission can delete roles', function () {
    $role = Role::create(['name' => 'test-role']);

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => 'test-token'])
        ->delete(route('admin.role.destroy', $role), ['_token' => 'test-token']);

    $response->assertRedirect();
    $this->assertDatabaseMissing('roles', ['name' => 'test-role']);
});

test('user without role management permission cannot create roles', function () {
    $roleData = [
        'name' => 'test-role',
        'permissions' => [],
        '_token' => 'test-token'
    ];

    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => 'test-token'])
        ->post(route('admin.role.store'), $roleData);

    $response->assertForbidden();
});

test('user without role management permission cannot update roles', function () {
    $role = Role::create(['name' => 'test-role']);
    $updateData = [
        'name' => 'updated-role',
        'permissions' => [],
        '_token' => 'test-token'
    ];

    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => 'test-token'])
        ->put(route('admin.role.update', $role), $updateData);

    $response->assertForbidden();
});

test('user without role management permission cannot delete roles', function () {
    $role = Role::create(['name' => 'test-role']);

    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => 'test-token'])
        ->delete(route('admin.role.destroy', $role), ['_token' => 'test-token']);

    $response->assertForbidden();
});
