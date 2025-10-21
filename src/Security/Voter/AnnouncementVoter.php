<?php

namespace App\Security\Voter;

use App\Domain\Entity\Attendee;
use App\Security\Enum\RoleEnum;
use App\Security\OidcUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AnnouncementVoter extends AbstractVoter
{
    public const string CREATE_ANNOUNCEMENT = 'create_announcement';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CREATE_ANNOUNCEMENT;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($user instanceof OidcUser) {
            return true;
        }

        if (!$user instanceof Attendee) {
            return false;
        }

        return match ($attribute) {
            self::CREATE_ANNOUNCEMENT => $this->canCreateAnnouncement($user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canCreateAnnouncement(Attendee $attendee): bool
    {
        return $this->userHasRole($attendee, RoleEnum::STAFF);
    }
}