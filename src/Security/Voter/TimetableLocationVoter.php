<?php

namespace App\Security\Voter;

use App\Domain\Entity\TimetableLocation;
use App\Domain\Enum\TimetableLocationType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TimetableLocationVoter extends Voter
{
    public const VIEW_LOCATION = 'view_location';
    public const string CREATE_LOCATION = 'create_location';
    public const string EDIT_LOCATION = 'edit_location';
    public const string DELETE_LOCATION = 'delete_location';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::VIEW_LOCATION && $subject instanceof TimetableLocationType) {
            return true;
        }

        if ($attribute === self::CREATE_LOCATION && $subject === null) {
            return true;
        }

        if (in_array($attribute, [self::EDIT_LOCATION, self::DELETE_LOCATION], true)) {
            return $subject instanceof TimetableLocation;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $roles = $token->getRoleNames();

        if ($attribute === self::VIEW_LOCATION) {
            if (!$subject instanceof TimetableLocationType) {
                return false;
            }

            if ($subject === TimetableLocationType::ROOM) {
                return true;
            }

            return in_array('ROLE_SUPER_ADMIN', $roles, true)
                || in_array('ROLE_ADMIN', $roles, true)
                || in_array('ROLE_VOLUNTEER', $roles, true);
        }

        return match ($attribute) {
            self::CREATE_LOCATION => in_array('ROLE_SUPER_ADMIN', $token->getRoleNames(), true)
                || in_array('ROLE_ADMIN', $roles, true),
            self::EDIT_LOCATION => (in_array('ROLE_SUPER_ADMIN', $token->getRoleNames(), true)
                    || in_array('ROLE_ADMIN', $roles, true)) && $subject instanceof TimetableLocation,
            self::DELETE_LOCATION => in_array('ROLE_SUPER_ADMIN', $token->getRoleNames(), true)
                && $subject instanceof TimetableLocation,
        };
    }
}