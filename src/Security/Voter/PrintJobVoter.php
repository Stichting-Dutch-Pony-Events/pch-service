<?php

namespace App\Security\Voter;

use App\Domain\Entity\Attendee;
use App\Domain\Entity\PrintJob;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PrintJobVoter extends Voter
{
    public const VIEW = 'view-print-job';
    public const CREATE = 'create-print-job';
    public const EDIT = 'edit-print-job';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($subject !== null && !$subject instanceof PrintJob) {
            return false;
        }

        if (!in_array($attribute, [self::VIEW, self::CREATE, self::EDIT], true)) {
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

        return in_array('ROLE_ADMIN', $user->getRoles(), true) || in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true);
    }
}