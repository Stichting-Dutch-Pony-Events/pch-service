<?php

namespace App\Security;

use App\Domain\Entity\Attendee;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AttendeeVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
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

        if (!$user instanceof Attendee) {
            return false;
        }

        /** @var Attendee $attendee */
        $attendee = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($attendee, $user),
            self::EDIT => $this->canEdit($attendee, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Attendee $attendee, Attendee $user): bool
    {
        return $this->canEdit($attendee, $user);
    }

    private function canEdit(Attendee $attendee, Attendee $user): bool
    {
        if ($attendee->getId() === $user->getId()) {
            return true;
        }

        return in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPER_ADMIN', $user->getRoles());
    }
}