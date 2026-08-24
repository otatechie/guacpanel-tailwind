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
            'view-dashboard' => 'Open the admin dashboard and its metrics',

            // User Management
            'manage-users' => 'Manage user accounts',
            'view-users' => 'View user accounts',
            'create-users' => 'Create user accounts',
            'edit-users' => 'Edit user accounts',
            'delete-users' => 'Delete user accounts',
            'edit-profile' => 'Edit own profile',
            'ban-users' => 'Ban/suspend user accounts',

            // System
            'manage-settings' => 'Manage system settings',
            'manage-security-settings' => 'Manage system security settings',
            'view-sessions' => 'See active sessions across the system',
            'manage-sessions' => 'Terminate user sessions',
            'view-health' => 'See uptime, service checks and diagnostics',

            // Audit & Monitoring
            'view-audits' => 'View system audit logs',
            'view-failed-jobs' => 'See jobs that failed on the queue',
            'manage-failed-jobs' => 'Retry and delete failed queue jobs',

            // Backup Management
            'manage-backups' => 'Create and manage system backups',
            'view-backups' => 'See existing backups and their details',

            // Personalisation
            'manage-personalisation' => 'Manage system appearance and branding',
            'view-personalisation' => 'View system appearance and branding',
            'update-personalisation' => 'Update system appearance and branding',
            'upload-personalisation-files' => 'Upload system appearance and branding files',
            'delete-personalisation-files' => 'Delete system appearance and branding files',

            // Roles & Permissions
            'manage-roles' => 'Manage user roles',
            'manage-permissions' => 'Manage user permissions',
            'view-permissions-roles' => 'View permissions and roles',

            // Login History
            'view-login-history' => 'View user login history',
            'manage-login-history' => 'Delete user login history records',

            // App Notifications
            'view-notifications' => 'User Can View Notifications',
            'edit-notifications' => 'User Can Edit, Mark as Read, and Dismiss Notifications',
            'create-notifications' => 'User Can Create Notifications',
            'delete-notifications' => 'User Can Delete Notifications',

            'manage-notifications' => 'Admin Can Manage Notifications',

            // User Impersonation
            'impersonate-users' => 'Admin Can Impersonate Users',
        ];

        $permissions = [];

        foreach ($permissionData as $name => $description) {
            /* updateOrCreate, not firstOrCreate: descriptions are copy, and a
               reseed left every existing row on whatever text it was created
               with. Grants live on permission ids, so rewriting the text here
               does not touch who has what. */
            $permissions[$name] = Permission::updateOrCreate(['name' => $name], ['description' => $description]);
        }

        return $permissions;
    }

    private function createRoles(): array
    {
        $roleData = [
            // Shown beneath a heading that already names the role, so neither
            // opens by repeating it.
            'superuser' => 'Full access to every part of the system',
            'user' => 'Limited access for standard accounts',
        ];

        $roles = [];

        foreach ($roleData as $name => $description) {
            $roles[$name] = Role::updateOrCreate(['name' => $name], ['description' => $description]);
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
            $permissions['view-dashboard'],
            $permissions['view-notifications'],
            $permissions['edit-notifications'],
            $permissions['create-notifications'],
            $permissions['delete-notifications'],
        ]);
    }
}
