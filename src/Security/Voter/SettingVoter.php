<?php

namespace App\Security\Voter;

use App\Domain\Entity\Attendee;
use App\Domain\Entity\PrintJob;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SettingVoter extends Voter
{
    public const EDIT_SETTINGS = 'edit_settings';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($subject !== null && !$subject instanceof PrintJob) {
            return false;
        }

        if ($attribute !== self::EDIT_SETTINGS) {
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

        return in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
    }
}