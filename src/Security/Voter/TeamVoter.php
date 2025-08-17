<?php

namespace App\Security\Voter;

use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TeamVoter extends AbstractVoter
{
    public const CREATE_TEAM = 'CREATE_TEAM';
    public const EDIT_TEAM = 'EDIT_TEAM';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE_TEAM, self::EDIT_TEAM]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->userHasRole($token, RoleEnum::STAFF);
    }
}