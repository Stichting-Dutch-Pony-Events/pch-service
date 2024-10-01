<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Domain\Enum\AdminCommandType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminCommandVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return AdminCommandType::tryFrom($attribute) !== null;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return in_array('ROLE_SUPER_ADMIN', $token->getRoleNames(), true);
    }
}
