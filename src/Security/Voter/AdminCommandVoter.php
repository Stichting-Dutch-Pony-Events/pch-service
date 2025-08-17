<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Domain\Enum\AdminCommandType;
use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AdminCommandVoter extends AbstractVoter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return AdminCommandType::tryFrom($attribute) !== null;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->userHasRole($token, RoleEnum::SUPER_ADMIN);
    }
}
