<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CheckInVoter extends Voter
{
    public const CHECK_IN = 'check_in';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CHECK_IN;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        foreach (['ROLE_VOLUNTEER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'] as $role) {
            if (in_array($role, $token->getRoleNames(),true)) {
                return true;
            }
        }

        return false;
    }
}
