<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manage permissions and roles']);
    
    $this->adminUser = User::factory()->create(['name' => 'Admin User']);
    $this->adminUser->givePermissionTo('manage permissions and roles');
    
    $this->regularUser = User::factory()->create(['name' => 'Regular User']);
    
    $this->testToken = 'test-token';
});

test('unauthenticated user cannot access role management page', function () {
    $this->get(route('admin.permission.role.index'))
        ->assertRedirect(route('login'));
});

test('user with manage permissions and roles permission can access role management page', function () {
    $this->actingAs($this->adminUser)
        ->withSession(['_token' => $this->testToken])
        ->get(route('admin.permission.role.index'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => 
            $page->component('Admin/PermissionRole/IndexPermissionRolePage')
        );
});

test('user without manage permissions and roles permission cannot access role management page', function () {
    $this->actingAs($this->regularUser)
        ->withSession(['_token' => $this->testToken])
        ->get(route('admin.permission.role.index'))
        ->assertForbidden();
}); 