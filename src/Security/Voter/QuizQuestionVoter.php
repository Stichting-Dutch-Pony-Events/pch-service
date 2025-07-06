<?php

namespace App\Security\Voter;

use App\Domain\Entity\QuizQuestion;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class QuizQuestionVoter extends Voter
{
    public const string CREATE_QUESTION = 'create_question';
    public const string EDIT_QUESTION = 'edit_question';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Check if the attribute is one of the defined constants
        if (!in_array($attribute, [self::CREATE_QUESTION, self::EDIT_QUESTION], true)) {
            return false;
        }

        // Check if the subject is null or an instance of the expected class
        return $subject === null || $subject instanceof QuizQuestion;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::EDIT_QUESTION, self::CREATE_QUESTION =>
                in_array('ROLE_ADMIN', $token->getRoleNames(), true) ||
                in_array('ROLE_SUPER_ADMIN', $token->getRoleNames(), true),
            default => false,
        };
    }
}