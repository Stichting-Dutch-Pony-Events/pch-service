<?php

namespace App\Domain\Service;

use App\Application\Request\QuizAnswerTeamWeightRequest;
use App\Domain\Entity\QuizAnswer;
use App\Domain\Entity\QuizAnswerTeamWeight;
use App\Domain\Entity\Team;
use App\Util\Exceptions\Exception\Common\InvalidInputException;

readonly class QuizAnswerTeamWeightDomainService
{
    /**
     * Validates the weights of teams in a quiz answer.
     * @param QuizAnswerTeamWeightRequest[] $answerTeamWeightRequest An array of team weight objects, each containing a 'weight' property.
     * @throws InvalidInputException
     */
    public function validateTeamWeights(array $answerTeamWeightRequest): bool
    {
        $totalWeight = 0;

        foreach ($answerTeamWeightRequest as $teamWeight) {
            if ($teamWeight->weight < 0 || $teamWeight->weight > 100) {
                throw new InvalidInputException(
                    "Team weight must be between 0 and 100, but got {$teamWeight->weight}."
                );
            }
            $totalWeight += $teamWeight->weight;
        }

        if ($totalWeight !== 100) {
            throw new InvalidInputException("Total team weights must sum to 100%, but got {$totalWeight}%.");
        }
        return true;
    }

    public function createTeamWeight(
        QuizAnswer                  $quizAnswer,
        Team                        $team,
        QuizAnswerTeamWeightRequest $quizAnswerTeamWeightRequest
    ): QuizAnswerTeamWeight {
        return new QuizAnswerTeamWeight(
            quizAnswer: $quizAnswer,
            team: $team,
            weight: $quizAnswerTeamWeightRequest->weight
        );
    }
}