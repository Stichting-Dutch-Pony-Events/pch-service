<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CheckInVoter extends AbstractVoter
{
    public const CHECK_IN = 'check_in';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CHECK_IN;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->userHasRole($token, RoleEnum::INFOBOOTH);
    }
}
