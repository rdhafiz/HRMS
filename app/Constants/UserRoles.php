<?php

namespace App\Constants;

class UserRoles
{
    public const SYSTEM_ADMIN = 1;
    public const HR_MANAGER = 2;
    public const SUPERVISOR = 3;

    public static function label(int $role): string
    {
        return match ($role) {
            self::SYSTEM_ADMIN => 'system_admin',
            self::HR_MANAGER => 'hr_manager',
            self::SUPERVISOR => 'supervisor',
            default => 'unknown',
        };
    }

    public static function fromString(string $name): ?int
    {
        return match (strtolower(trim($name))) {
            'system_admin' => self::SYSTEM_ADMIN,
            'hr_manager' => self::HR_MANAGER,
            'supervisor' => self::SUPERVISOR,
            default => null,
        };
    }
}

