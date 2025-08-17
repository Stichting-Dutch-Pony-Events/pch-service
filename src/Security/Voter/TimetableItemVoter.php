<?php

namespace App\Security\Voter;

use App\Domain\Entity\TimetableItem;
use App\Domain\Enum\TimetableLocationType;
use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TimetableItemVoter extends AbstractVoter
{
    public const VIEW_ITEM = 'view_item';
    public const string CREATE_ITEM = 'create_item';
    public const string EDIT_ITEM = 'edit_item';
    public const string DELETE_ITEM = 'delete_item';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::VIEW_ITEM && $subject instanceof TimetableLocationType) {
            return true;
        }

        if ($attribute === self::CREATE_ITEM && $subject === null) {
            return true;
        }

        if (in_array($attribute, [self::EDIT_ITEM, self::DELETE_ITEM], true)) {
            return $subject instanceof TimetableItem;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $roles = $token->getRoleNames();

        if ($attribute === self::VIEW_ITEM) {
            if (!$subject instanceof TimetableLocationType) {
                return false;
            }

            if ($subject === TimetableLocationType::ROOM) {
                return true;
            }

            return $this->userHasRole($token, RoleEnum::VOLUNTEER);
        }

        return match ($attribute) {
            self::CREATE_ITEM => $this->userHasRole($token, RoleEnum::INFOBOOTH),
            self::EDIT_ITEM => $this->userHasRole($token, RoleEnum::INFOBOOTH) && $subject instanceof TimetableItem,
            self::DELETE_ITEM => $this->userHasRole($token, RoleEnum::STAFF) && $subject instanceof TimetableItem,
            default => false,
        };
    }
}