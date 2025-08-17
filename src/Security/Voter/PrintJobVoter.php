<?php

namespace App\Security\Voter;

use App\Domain\Entity\PrintJob;
use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PrintJobVoter extends AbstractVoter
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
        return $this->userHasRole($token, RoleEnum::INFOBOOTH);
    }
}