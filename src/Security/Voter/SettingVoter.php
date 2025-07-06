<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SettingVoter extends Voter
{
    public const EDIT_SETTINGS = 'edit_settings';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT_SETTINGS;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return in_array('ROLE_SUPER_ADMIN', $token->getRoleNames(), true);
    }
}