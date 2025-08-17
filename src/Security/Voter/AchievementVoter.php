<?php

namespace App\Security\Voter;

use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AchievementVoter extends AbstractVoter
{
    public const AWARD_ACHIEVEMENT = 'AWARD_ACHIEVEMENT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::AWARD_ACHIEVEMENT;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->userHasRole($token, RoleEnum::VOLUNTEER);
    }
}