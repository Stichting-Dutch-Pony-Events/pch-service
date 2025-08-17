<?php

namespace App\Domain\Doctrine\Type;

use App\Security\Enum\RoleEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

class RoleEnumArrayType extends Type
{
    public const string NAME = 'role_enum_array';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            throw new InvalidArgumentException('Expected array of RoleEnum.');
        }

        // store as comma-separated string
        return implode(',', array_map(static fn(RoleEnum $r) => $r->value, $value));
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $parts = explode(',', $value);
        $roles = [];
        foreach ($parts as $part) {
            $role = RoleEnum::tryFrom($part);
            if ($role !== null) {
                $roles[] = $role;
            }
        }

        return $roles;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}