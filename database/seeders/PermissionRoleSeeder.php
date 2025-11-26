<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->createPermissions();
        $roles = $this->createRoles();
        $this->assignPermissions($roles, $permissions);
    }

    private function createPermissions(): array
    {
        $permissionData = [
            // User Management
            'manage-users' => 'Manage user accounts',
            'view-users'   => 'View user accounts',
            'edit-profile' => 'Edit own profile',
            'ban-users'    => 'Ban/suspend user accounts',

            // System
            'access-dashboard'         => 'Access admin dashboard',
            'manage-settings'          => 'Manage system settings',
            'manage-security-settings' => 'Manage system security settings',

            // Audit & Monitoring
            'view-audits' => 'View system audit logs',

            // Backup Management
            'manage-backups' => 'Create and manage system backups',

            // Personalization
            'manage-personalization' => 'Manage system appearance and branding',

            // Roles & Permissions
            'manage-roles'           => 'Manage user roles',
            'manage-permissions'     => 'Manage user permissions',
            'view-permissions-roles' => 'View permissions and roles',

            // Login History
            'view-login-history' => 'View user login history',
        ];

        $permissions = [];

        foreach ($permissionData as $name => $description) {
            $permissions[$name] = Permission::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        return $permissions;
    }

    private function createRoles(): array
    {
        $roleData = [
            'superuser' => 'Superuser with full system access',
            'user'      => 'Standard user with limited permissions',
        ];

        $roles = [];

        foreach ($roleData as $name => $description) {
            $roles[$name] = Role::updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        return $roles;
    }

    private function assignPermissions(array $roles, array $permissions): void
    {
        // Superuser gets all permissions
        $roles['superuser']->syncPermissions(array_values($permissions));

        // Regular user permissions
        $roles['user']->syncPermissions([
            $permissions['edit-profile'],
        ]);
    }
}
