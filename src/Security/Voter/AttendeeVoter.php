<?php

namespace App\Security\Voter;

use App\Domain\Entity\Attendee;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AttendeeVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const EDIT_ROLES = 'edit_roles';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::EDIT_ROLES])) {
            return false;
        }

        if (!$subject instanceof Attendee) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Attendee $attendee */
        $attendee = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($attendee, $user),
            self::EDIT => $this->canEdit($attendee, $user),
            self::EDIT_ROLES => $this->canEditRoles($user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Attendee $attendee, UserInterface $user): bool
    {
        if (($user instanceof Attendee) && $attendee->getId() === $user->getId()) {
            return true;
        }

        return in_array('ROLE_VOLUNTEER', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    private function canEdit(Attendee $attendee, UserInterface $user): bool
    {
        if (($user instanceof Attendee) && $attendee->getId() === $user->getId()) {
            return true;
        }

        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    private function canEditRoles(UserInterface $user): bool
    {
        return in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
    }
}
