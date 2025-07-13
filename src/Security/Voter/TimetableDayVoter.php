<?php

namespace App\Security\Voter;

use App\Domain\Entity\TimetableDay;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TimetableDayVoter extends Voter
{
    public const string CREATE_DAY = 'create_day';
    public const string EDIT_DAY = 'edit_day';
    public const string DELETE_DAY = 'delete_day';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Check if the attribute is one of the defined constants
        if (!in_array($attribute, [self::CREATE_DAY, self::EDIT_DAY, self::DELETE_DAY], true)) {
            return false;
        }

        // Check if the subject is null or an instance of the expected class
        return $subject === null || $subject instanceof TimetableDay;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::CREATE_DAY, self::EDIT_DAY => in_array('ROLE_SUPER_ADMIN', $token->getRoleNames(), true),
            self::DELETE_DAY =>
                in_array('ROLE_SUPER_ADMIN', $token->getRoleNames(), true) && $subject instanceof TimetableDay,
            default => false,
        };
    }
}