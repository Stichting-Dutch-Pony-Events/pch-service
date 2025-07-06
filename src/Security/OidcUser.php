<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class OidcUser implements UserInterface
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

    public function eraseCredentials(): void
    {
        return;
    }
}