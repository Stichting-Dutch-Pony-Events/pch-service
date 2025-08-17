<?php

namespace App\Security\Voter;

use App\Domain\Entity\Contract\EnumUserInterface;
use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractVoter extends Voter
{
    public function getUserRoles(TokenInterface|UserInterface $token): array
    {
        $user = $token;
        if ($token instanceof TokenInterface) {
            $user = $token->getUser();
        }

        if (!$user instanceof EnumUserInterface) {
            return [];
        }

        $roles = [];
        foreach ($user->getUserRoles() as $role) {
            $roles = [...$roles, ...$role->getRoles()];
        }

        return RoleEnum::deduplicate($roles);
    }

    public function userHasRole(TokenInterface|UserInterface $token, RoleEnum $role): bool
    {
        return in_array($role, $this->getUserRoles($token), true);
    }
}