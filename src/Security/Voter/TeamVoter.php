<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeamVoter extends Voter
{
    public const CREATE_TEAM = 'CREATE_TEAM';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CREATE_TEAM;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $roles = $token->getRoleNames();

        return in_array('ROLE_SUPER_ADMIN', $roles, true);
    }
}