<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view-permissions-roles']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('view-permissions-roles');

    $this->regularUser = User::factory()->create();

    $this->testToken = 'test-token';
});

test('it redirects unauthenticated users to login page', function () {
    $this->get(route('admin.permission.role.index'))->assertRedirect(route('login'));
});

test('it allows access to users with manage permissions and roles permission', function () {
    $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->get(route('admin.permission.role.index'))
        ->assertStatus(200)
        ->assertInertia(fn($page) => $page->component('Admin/PermissionRole/IndexPermissionRolePage'));
});

test('it denies access to users without manage permissions and roles permission', function () {
    $this->actingAs($this->regularUser)
        ->withSession(['_token' => $this->testToken])
        ->get(route('admin.permission.role.index'))
        ->assertForbidden();
});

test('it marks system roles as protected so the ui can hide their controls', function () {
    Role::firstOrCreate(['name' => 'superuser']);
    Role::firstOrCreate(['name' => 'editor']);

    $response = $this->actingAs($this->adminUser)->get(route('admin.permission.role.index'));

    // Without is_protected the tab's edit/delete guards never fire, so every
    // action on a system role opened a form the server then refused.
    $response->assertInertia(
        fn($page) => $page->where(
            'roles',
            fn($roles) => collect($roles)->firstWhere('name', 'superuser')['is_protected'] === true &&
                collect($roles)->firstWhere('name', 'editor')['is_protected'] === false,
        ),
    );
});
