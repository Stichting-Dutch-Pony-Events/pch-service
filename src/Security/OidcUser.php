<?php

namespace App\Security;

use App\Domain\Entity\Contract\EnumUserInterface;
use App\Security\Enum\RoleEnum;

class OidcUser implements EnumUserInterface
{
    public function __construct(
        private string $identifier,
        private array  $claims
    ) {
    }


    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public function getClaims(): array
    {
        return $this->claims;
    }

    public function getRoles(): array
    {
        return $this->claims['roles'] ?? ['ROLE_USER'];
    }

    /**
     * @return RoleEnum[]
     */
    public function getUserRoles(): array
    {
        $roles = $this->getRoles();
        $roleEnums = [];

        foreach ($roles as $role) {
            $enumRole = RoleEnum::tryFrom($role);
            if ($enumRole !== null) {
                $roleEnums[] = $enumRole;
            }
        }

        return $roleEnums;
    }

    public function eraseCredentials(): void
    {
        return;
    }
}