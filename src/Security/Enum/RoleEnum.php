<?php

namespace App\Security\Enum;

enum RoleEnum: string
{
    case USER = 'ROLE_USER';
    case VOLUNTEER = 'ROLE_VOLUNTEER';
    case INFOBOOTH = 'ROLE_INFOBOOTH';
    case STAFF = 'ROLE_STAFF';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public function getRoles(): array
    {
        return match ($this) {
            self::USER => [self::USER->value],
            self::VOLUNTEER => [self::USER->value, self::VOLUNTEER->value],
            self::INFOBOOTH => [self::USER->value, self::VOLUNTEER->value, self::INFOBOOTH->value],
            self::STAFF => [
                self::USER->value,
                self::VOLUNTEER->value,
                self::INFOBOOTH->value,
                self::STAFF->value
            ],
            self::SUPER_ADMIN => [
                self::USER->value,
                self::VOLUNTEER->value,
                self::INFOBOOTH->value,
                self::STAFF->value,
                self::SUPER_ADMIN->value
            ],
        };
    }
}
