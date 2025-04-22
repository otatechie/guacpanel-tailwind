<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

test('admin can view users list', function () {
    $users = User::factory()->count(5)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/User/IndexUserPage')
            ->has('users.data', 6) // 5 created + 1 admin
            ->has('users.data.0', fn (Assert $user) => $user
                ->hasAll(['id', 'name', 'email', 'roles', 'permissions'])
                ->etc()
            )
        );
});

test('admin can view user edit page', function () {
    $user = User::factory()->create();
    $roles = Role::factory()->count(2)->create();
    $permissions = Permission::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.users.edit', $user))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/User/EditUserPage')
            ->has('user', fn (Assert $userData) => $userData
                ->hasAll(['id', 'name', 'email', 'roles', 'permissions'])
                ->etc()
            )
            ->has('permissions.data', 3)
            ->has('roles.data', 2)
        );
});

test('admin can update user details', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permissions = Permission::factory()->count(2)->create();

    $this->actingAs($this->admin)
        ->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => $role->id,
            'permissions' => $permissions->pluck('id')->toArray(),
            'disable_account' => false,
            'force_password_change' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'force_password_change' => true,
    ]);

    $this->assertTrue($user->fresh()->hasRole($role->name));
    $this->assertTrue($user->fresh()->hasAllPermissions($permissions->pluck('name')->toArray()));
});

test('admin cannot update user with invalid data', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($this->admin)
        ->put(route('admin.users.update', $user), [
            'name' => '',
            'email' => $otherUser->email, // Duplicate email
            'role' => 999, // Non-existent role
            'permissions' => [999], // Non-existent permission
        ])
        ->assertSessionHasErrors(['name', 'email', 'role', 'permissions.0']);
});

test('admin cannot disable account and force password change simultaneously', function () {
    $user = User::factory()->create();

    $this->actingAs($this->admin)
        ->put(route('admin.users.update', $user), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'disable_account' => true,
            'force_password_change' => true,
        ])
        ->assertSessionHasErrors(['error']);
});

test('admin can delete user', function () {
    $user = User::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('non-admin cannot access user management', function () {
    $regularUser = User::factory()->create();

    $this->actingAs($regularUser)
        ->get(route('admin.users.index'))
        ->assertForbidden();

    $this->actingAs($regularUser)
        ->get(route('admin.users.edit', $this->admin))
        ->assertForbidden();

    $this->actingAs($regularUser)
        ->put(route('admin.users.update', $this->admin), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ])
        ->assertForbidden();

    $this->actingAs($regularUser)
        ->delete(route('admin.users.destroy', $this->admin))
        ->assertForbidden();
});
