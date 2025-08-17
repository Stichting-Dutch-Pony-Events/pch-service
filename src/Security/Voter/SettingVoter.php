<?php

namespace App\Security\Voter;

use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SettingVoter extends AbstractVoter
{
    public const EDIT_SETTINGS = 'edit_settings';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT_SETTINGS;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->userHasRole($token, RoleEnum::STAFF);
    }
}