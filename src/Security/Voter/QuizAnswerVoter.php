<?php

namespace App\Security\Voter;

use App\Domain\Entity\QuizAnswer;
use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class QuizAnswerVoter extends AbstractVoter
{
    public const string CREATE_ANSWER = 'create_answer';
    public const string EDIT_ANSWER = 'edit_answer';
    public const string DELETE_ANSWER = 'delete_answer';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Check if the attribute is one of the defined constants
        if (!in_array($attribute, [self::CREATE_ANSWER, self::EDIT_ANSWER, self::DELETE_ANSWER], true)) {
            return false;
        }

        // Check if the subject is null or an instance of the expected class
        return $subject === null || $subject instanceof QuizAnswer;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::CREATE_ANSWER, self::EDIT_ANSWER => $this->userHasRole($token, RoleEnum::INFOBOOTH),
            self::DELETE_ANSWER => $this->userHasRole($token, RoleEnum::STAFF),
            default => false,
        };
    }
}