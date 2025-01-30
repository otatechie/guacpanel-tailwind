<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Platform administrator with full access'
            ],
            [
                'name' => 'seller',
                'description' => 'A verified user who can list and sell second-hand gadgets on the platform.'
            ],
            [
                'name' => 'moderator',
                'description' => 'Platform guardian who ensures quality, safety, and trust in the marketplace.'
            ]
        ];

        $permissions = [
            // Listing Management
            [
                'name' => 'create-listing',
                'description' => 'Create new product listings'
            ],
            [
                'name' => 'edit-own-listing',
                'description' => 'Edit own product listings'
            ],
            [
                'name' => 'delete-listing',
                'description' => 'Remove listings from platform'
            ],
            [
                'name' => 'manage-all-listings',
                'description' => 'Manage all product listings'
            ],

            // User Management
            [
                'name' => 'manage-users',
                'description' => 'Manage user accounts'
            ],
            [
                'name' => 'edit-profile',
                'description' => 'Edit own profile'
            ],
            [
                'name' => 'ban-users',
                'description' => 'Ban/suspend user accounts'
            ],

            // Moderation
            [
                'name' => 'review-listings',
                'description' => 'Review and verify listings'
            ],
            [
                'name' => 'handle-reports',
                'description' => 'Handle user reports'
            ],
            [
                'name' => 'flag-content',
                'description' => 'Flag suspicious content'
            ],
            [
                'name' => 'verify-sellers',
                'description' => 'Verify seller accounts'
            ],

            // System
            [
                'name' => 'access-dashboard',
                'description' => 'Access admin dashboard'
            ],
            [
                'name' => 'manage-settings',
                'description' => 'Manage system settings'
            ]
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role['name'],
                'description' => $role['description']
            ]);
        }

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'description' => $permission['description']
            ]);
        }
    }
}
