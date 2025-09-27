<?php

namespace App\Security\Enum;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "RoleEnum",
    type: "string",
    enum: ["ROLE_USER", "ROLE_VOLUNTEER", "ROLE_INFOBOOTH", "ROLE_STAFF", "ROLE_SUPER_ADMIN"],
)]
enum RoleEnum: string
{
    case USER = 'ROLE_USER';
    case VOLUNTEER = 'ROLE_VOLUNTEER';
    case INFOBOOTH = 'ROLE_INFOBOOTH';
    case STAFF = 'ROLE_STAFF';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @return RoleEnum[]
     */
    public function getRoles(): array
    {
        return match ($this) {
            self::USER => [self::USER],
            self::VOLUNTEER => [self::USER, self::VOLUNTEER],
            self::INFOBOOTH => [self::USER, self::VOLUNTEER, self::INFOBOOTH],
            self::STAFF => [
                self::USER,
                self::VOLUNTEER,
                self::INFOBOOTH,
                self::STAFF
            ],
            self::SUPER_ADMIN => [
                self::USER,
                self::VOLUNTEER,
                self::INFOBOOTH,
                self::STAFF,
                self::SUPER_ADMIN
            ],
        };
    }

    /**
     * @param RoleEnum[] $roles
     * @return RoleEnum[]
     */
    public static function deduplicate(array $roles): array
    {
        return array_values(
            array_reduce(
                $roles,
                static function (array $carry, RoleEnum $role) {
                    $carry[$role->value] = $role;
                    return $carry;
                },
                []
            )
        );
    }
}
