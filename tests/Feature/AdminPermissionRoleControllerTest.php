<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view-permissions-roles']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('view-permissions-roles');

    $this->regularUser = User::factory()->create();

    $this->testToken = 'test-token';
});

test('it redirects unauthenticated users to login page', function () {
    $this->get(route('admin.permission.role.index'))
        ->assertRedirect(route('login'));
});

test('it allows access to users with manage permissions and roles permission', function () {
    $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->get(route('admin.permission.role.index'))
        ->assertStatus(200)
        ->assertInertia(
            fn ($page) => $page->component('Admin/PermissionRole/IndexPermissionRolePage')
        );
});

test('it denies access to users without manage permissions and roles permission', function () {
    $this->actingAs($this->regularUser)
        ->withSession(['_token' => $this->testToken])
        ->get(route('admin.permission.role.index'))
        ->assertForbidden();
});
