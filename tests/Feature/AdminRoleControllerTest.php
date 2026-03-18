<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manage-roles']);

    $this->superuserRole = Role::firstOrCreate(['name' => 'superuser']);
    $this->userRole = Role::firstOrCreate(['name' => 'user']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('manage-roles');

    $this->regularUser = User::factory()->create();
});

test('it redirects unauthenticated users to login page', function () {
    $response = $this->get(route('admin.permission.role.index'));
    $response->assertRedirect(route('login'));
});

test('it denies access to users without role management permission', function () {
    $response = $this->actingAs($this->regularUser)
        ->get(route('admin.permission.role.index'));
    $response->assertForbidden();
});

test('it allows users with role management permission to view the page', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.permission.role.index'));

    $response->assertInertia(
        fn ($assert) => $assert
        ->component('Admin/PermissionRole/IndexPermissionRolePage')
        ->has('permissions')
        ->has('roles')
        ->has('users')
    );
});

test('it allows users with role management permission to create roles', function () {
    $roleData = [
        'name'        => 'test-role',
        'permissions' => [],
        '_token'      => 'test-token',
    ];

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => 'test-token'])
        ->post(route('admin.role.store'), $roleData);

    $response->assertRedirect();
    $this->assertDatabaseHas('roles', ['name' => 'test-role']);
});

test('it allows users with role management permission to update roles', function () {
    $role = Role::create(['name' => 'test-role']);
    $updateData = [
        'name'        => 'updated-role',
        'permissions' => [],
        '_token'      => 'test-token',
    ];

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => 'test-token'])
        ->put(route('admin.role.update', $role), $updateData);

    $response->assertRedirect();
    $this->assertDatabaseHas('roles', ['name' => 'updated-role']);
});

test('it allows users with role management permission to delete roles', function () {
    $role = Role::create(['name' => 'test-role']);

    $response = $this->actingAs($this->adminUser)
        ->withSession(['_token' => 'test-token'])
        ->delete(route('admin.role.destroy', $role), ['_token' => 'test-token']);

    $response->assertRedirect();
    $this->assertDatabaseMissing('roles', ['name' => 'test-role']);
});

test('it denies role creation to users without role management permission', function () {
    $roleData = [
        'name'        => 'test-role',
        'permissions' => [],
        '_token'      => 'test-token',
    ];

    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => 'test-token'])
        ->post(route('admin.role.store'), $roleData);

    $response->assertForbidden();
});

test('it denies role update to users without role management permission', function () {
    $role = Role::create(['name' => 'test-role']);
    $updateData = [
        'name'        => 'updated-role',
        'permissions' => [],
        '_token'      => 'test-token',
    ];

    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => 'test-token'])
        ->put(route('admin.role.update', $role), $updateData);

    $response->assertForbidden();
});

test('it denies role deletion to users without role management permission', function () {
    $role = Role::create(['name' => 'test-role']);

    $response = $this->actingAs($this->regularUser)
        ->withSession(['_token' => 'test-token'])
        ->delete(route('admin.role.destroy', $role), ['_token' => 'test-token']);

    $response->assertForbidden();
});
