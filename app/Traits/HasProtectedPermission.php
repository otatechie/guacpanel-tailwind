<?php

namespace App\Traits;

trait HasProtectedPermission
{
    protected function getProtectedPermissions(): array
    {
        return [
            'manage-users',
            'ban-users',
            'access-dashboard',
            'manage-settings',
            'manage-security-settings',
            'view-audits',
            'manage-backups',
            'manage-personalization',
            'manage-roles',
            'manage-permissions',
            'view-permissions-roles',
            'view-login-history',
        ];
    }

    protected function isProtectedPermission(string $permissionName): bool
    {
        $protectedPermissions = array_map('strtolower', $this->getProtectedPermissions());

        return in_array(strtolower(trim($permissionName)), $protectedPermissions);
    }

    protected function getProtectedPermissionsForValidation(): string
    {
        return implode(',', $this->getProtectedPermissions());
    }
}
