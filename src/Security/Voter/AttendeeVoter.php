<?php

namespace App\Security\Voter;

use App\Domain\Entity\Attendee;
use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AttendeeVoter extends AbstractVoter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const EDIT_ROLES = 'edit_roles';
    public const RESET_PASSWORD = 'reset_password';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::EDIT_ROLES, self::RESET_PASSWORD])) {
            return false;
        }

        if ($subject !== null && !$subject instanceof Attendee) {
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

        /** @var Attendee|null $attendee */
        $attendee = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($attendee, $user),
            self::EDIT => $this->canEdit($attendee, $user),
            self::EDIT_ROLES => $this->canEditRoles($user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(?Attendee $attendee, UserInterface $user): bool
    {
        if ($attendee !== null && ($user instanceof Attendee) && $attendee->getId() === $user->getId()) {
            return true;
        }

        return $this->userHasRole($user, RoleEnum::VOLUNTEER);
    }

    private function canEdit(Attendee $attendee, UserInterface $user): bool
    {
        if (($user instanceof Attendee) && $attendee->getId() === $user->getId()) {
            return true;
        }

        return $this->userHasRole($user, RoleEnum::INFOBOOTH);
    }

    private function canEditRoles(UserInterface $user): bool
    {
        return $this->userHasRole($user, RoleEnum::STAFF);
    }

    private function resetPassword(Attendee $attendee, UserInterface $user): bool
    {
        if ($this->userHasRole($user, RoleEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($this->userHasRole($user, RoleEnum::STAFF) && !$this->userHasRole($attendee, RoleEnum::SUPER_ADMIN)) {
            return true;
        }

        if ($this->userHasRole($user, RoleEnum::INFOBOOTH) && !$this->userHasRole($attendee, RoleEnum::STAFF)) {
            return true;
        }

        return false;
    }
}
