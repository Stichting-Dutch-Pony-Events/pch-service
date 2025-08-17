<?php

namespace App\Security\Voter;

use App\Domain\Entity\TimetableLocation;
use App\Domain\Enum\TimetableLocationType;
use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TimetableLocationVoter extends AbstractVoter
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

            return $this->userHasRole($token, RoleEnum::VOLUNTEER);
        }

        return match ($attribute) {
            self::CREATE_LOCATION => $this->userHasRole($token, RoleEnum::INFOBOOTH),
            self::EDIT_LOCATION => $this->userHasRole($token, RoleEnum::INFOBOOTH)
                && $subject instanceof TimetableLocation,
            self::DELETE_LOCATION => $this->userHasRole($token, RoleEnum::STAFF)
                && $subject instanceof TimetableLocation,
        };
    }
}