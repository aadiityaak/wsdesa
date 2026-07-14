<?php

namespace App\Concerns;

trait HasRole
{
    public const ROLES = [
        'super_admin',
        'kepala_desa',
        'sekdes',
        'kasi_pemerintahan',
        'kasi_kesra',
        'kasi_pelayanan',
        'kaur_umum',
        'kaur_keuangan',
        'operator',
    ];

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role !== null;
    }
}
