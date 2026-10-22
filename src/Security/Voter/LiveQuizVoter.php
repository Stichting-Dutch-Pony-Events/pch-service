<?php

namespace App\Security\Voter;

use App\Domain\Entity\LiveQuizSession;
use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LiveQuizVoter extends AbstractVoter
{
    public const string CREATE_SESSION = 'live_quiz_create_session';
    public const string LIST_SESSIONS = 'live_quiz_list_sessions';
    public const string MANAGE_QUESTION = 'live_quiz_manage_question';
    public const string DEACTIVATE_SESSION = 'live_quiz_deactivate_session';
    public const string JOIN_SESSION = 'live_quiz_join_session';
    public const string SUBMIT_RESPONSE = 'live_quiz_submit_response';
    public const string VIEW_CURRENT = 'live_quiz_view_current';
    public const string VIEW_RESULTS = 'live_quiz_view_results';

    private const array STAFF_ATTRIBUTES = [
        self::CREATE_SESSION,
        self::LIST_SESSIONS,
        self::MANAGE_QUESTION,
        self::DEACTIVATE_SESSION,
        self::VIEW_RESULTS,
    ];

    private const array USER_ATTRIBUTES = [
        self::JOIN_SESSION,
        self::SUBMIT_RESPONSE,
        self::VIEW_CURRENT,
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        $allAttributes = [...self::STAFF_ATTRIBUTES, ...self::USER_ATTRIBUTES];

        if (!in_array($attribute, $allAttributes, true)) {
            return false;
        }

        return $subject === null || $subject instanceof LiveQuizSession;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match (true) {
            in_array($attribute, self::STAFF_ATTRIBUTES, true) => $this->userHasRole($token, RoleEnum::STAFF),
            in_array($attribute, self::USER_ATTRIBUTES, true) => $this->userHasRole($token, RoleEnum::USER),
            default => false,
        };
    }
}
