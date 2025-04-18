<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('unauthenticated users cannot access login history page', function () {
    $this->get(route('admin.login.history.index'))
        ->assertRedirect(route('login'));
        });

test('authenticated users can view login history page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.login.history.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/IndexLoginHistoryPage')
        );
});
