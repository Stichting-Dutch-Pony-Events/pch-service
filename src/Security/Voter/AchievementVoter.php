<?php

namespace App\Security\Voter;

use App\Domain\Entity\Attendee;
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
        $user = $token->getUser();

        if (!$user instanceof Attendee) {
            return false;
        }

        return in_array('ROLE_VOLUNTEER', $user->getRoles(), true)
            || in_array('ROLE_ADMIN', $user->getRoles(), true)
            || in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
    }
}