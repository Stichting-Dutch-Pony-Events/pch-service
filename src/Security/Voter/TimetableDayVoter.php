<?php

namespace App\Security\Voter;

use App\Domain\Entity\TimetableDay;
use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TimetableDayVoter extends AbstractVoter
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
        return $this->userHasRole($token, RoleEnum::STAFF);
    }
}