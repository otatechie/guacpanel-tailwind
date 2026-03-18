<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'view-login-history']);

    $this->adminUser = User::factory()->create();
    $this->adminUser->givePermissionTo('view-login-history');

    $this->regularUser = User::factory()->create();
});

test('it redirects unauthenticated users to login page', function () {
    $this->get(route('admin.login.history.index'))
        ->assertRedirect(route('login'));
});

test('it denies access to users without login history permission', function () {
    $this->actingAs($this->regularUser)
        ->get(route('admin.login.history.index'))
        ->assertForbidden();
});

test('it allows access to users with login history permission', function () {
    $this->actingAs($this->adminUser)
        ->get(route('admin.login.history.index'))
        ->assertStatus(200)
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('Admin/IndexLoginHistoryPage')
            ->has('loginHistory')
        );
});
