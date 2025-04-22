<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create required permissions
    Permission::firstOrCreate(['name' => 'view users']);
    Permission::firstOrCreate(['name' => 'edit users']);
    Permission::firstOrCreate(['name' => 'delete users']);
    Permission::firstOrCreate(['name' => 'manage users']);

    // Create test users with different permission levels
    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo([
        'view users',
        'edit users',
        'delete users',
        'manage users'
    ]);

    $this->viewOnlyUser = User::factory()->create();
    $this->viewOnlyUser->givePermissionTo('view users');

    $this->regularUser = User::factory()->create();
});

test('user with view permission can access user index page', function () {
    $response = $this->actingAs($this->viewOnlyUser)
        ->get(route('admin.user.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('Admin/User/IndexUserPage')
            ->has('users')
    );
});

test('user without view permission cannot access user index page', function () {
    $response = $this->actingAs($this->regularUser)
        ->get(route('admin.user.index'));

    $response->assertForbidden();
});

test('admin can create new user', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.user.store'), $userData);

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
});

test('admin cannot create user with invalid data', function () {
    $userData = [
        'name' => '', // invalid - empty
        'email' => 'not-an-email', // invalid format
        'password' => 'pass', // too short
        'password_confirmation' => 'different', // doesn't match
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.user.store'), $userData);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

test('admin cannot create user with duplicate email', function () {
    $existingUser = User::factory()->create();

    $userData = [
        'name' => 'Test User',
        'email' => $existingUser->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.user.store'), $userData);

    $response->assertSessionHasErrors(['email']);
});

test('admin can update user', function () {
    $user = User::factory()->create();
    $updatedData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $response = $this->actingAs($this->adminUser)
        ->put(route('admin.user.update', $user), $updatedData);

    $this->assertDatabaseHas('users', $updatedData);
});

test('admin cannot update user with invalid data', function () {
    $user = User::factory()->create();
    $invalidData = [
        'name' => '', // invalid - empty
        'email' => 'not-an-email', // invalid format
    ];

    $response = $this->actingAs($this->adminUser)
        ->put(route('admin.user.update', $user), $invalidData);

    $response->assertSessionHasErrors(['name', 'email']);
});

test('admin cannot update user email to existing email', function () {
    $existingUser = User::factory()->create();
    $userToUpdate = User::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->put(route('admin.user.update', $userToUpdate), [
            'name' => 'New Name',
            'email' => $existingUser->email
        ]);

    $response->assertSessionHasErrors(['email']);
});

test('admin can delete user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->delete(route('admin.user.destroy', $user));

    $this->assertDatabaseMissing('users', [
        'id' => $user->id
    ]);
});

test('user with view permission cannot create users', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->actingAs($this->viewOnlyUser)
        ->post(route('admin.user.store'), $userData);

    $response->assertForbidden();
});

test('user with view permission cannot update users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->viewOnlyUser)
        ->put(route('admin.user.update', $user), [
            'name' => 'Updated Name'
        ]);

    $response->assertForbidden();
});

test('user with view permission cannot delete users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->viewOnlyUser)
        ->delete(route('admin.user.destroy', $user));

    $response->assertForbidden();
});
