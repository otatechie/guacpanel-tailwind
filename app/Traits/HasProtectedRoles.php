<?php

namespace App\Traits;

trait HasProtectedRoles
{
    protected function getProtectedRoles(): array
    {
        return ['superuser', 'user'];
    }

    protected function isProtectedRole(string $roleName): bool
    {
        $protectedRoles = array_map('strtolower', $this->getProtectedRoles());

        return in_array(strtolower(trim($roleName)), $protectedRoles);
    }

    protected function getProtectedRolesForValidation(): string
    {
        return implode(',', $this->getProtectedRoles());
    }
}
