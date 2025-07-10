<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AchievementVoter extends Voter
{
    public const AWARD_ACHIEVEMENT = 'AWARD_ACHIEVEMENT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::AWARD_ACHIEVEMENT;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $roles = $token->getRoleNames();

        return in_array('ROLE_VOLUNTEER', $roles, true)
            || in_array('ROLE_ADMIN', $roles, true)
            || in_array('ROLE_SUPER_ADMIN', $roles, true);
    }
}