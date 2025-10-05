<?php

namespace App\Domain\Service;

use App\Domain\Constants;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\CharacterQuizSubmission;
use App\Domain\Entity\CharacterQuizSubmissionTeamResult;
use App\Util\Exceptions\Exception\Entity\EntityNotPersistedException;

readonly class CharacterQuizSubmissionDomainService
{
    public function createCharacterQuizSubmission(Attendee $attendee): CharacterQuizSubmission
    {
        return new CharacterQuizSubmission($attendee);
    }

    /**
     * @throws EntityNotPersistedException
     */
    public function calculateResults(CharacterQuizSubmission $characterQuizSubmission): CharacterQuizSubmission
    {
        /** @var array<string, CharacterQuizSubmissionTeamResult> $teamScores */
        $teamScores = [];
        $totalPoints = 0;

        foreach ($characterQuizSubmission->getAnswers() as $answer) {
            foreach ($answer->getAnswer()->getQuizAnswerTeamWeights() as $quizAnswerTeamWeight) {
                $team = $quizAnswerTeamWeight->getTeam();

                if (array_key_exists($team->getId(), $teamScores)) {
                    $teamScore = $teamScores[$team->getId()];
                    $teamScore->setPoints($teamScore->getPoints() + $quizAnswerTeamWeight->getWeight());
                } else {
                    $teamScores[$team->getId()] = new CharacterQuizSubmissionTeamResult(
                        team:       $team,
                        points:     $quizAnswerTeamWeight->getWeight(),
                        percentage: 0,
                        submission: $characterQuizSubmission
                    );
                }

                $totalPoints += $quizAnswerTeamWeight->getWeight();
            }
        }

        $totalPercentage = 0;
        foreach ($teamScores as $teamScore) {
            $percentage = (int)round(Constants::HUNDRED_PERCENT / $totalPoints * $teamScore->getPoints());
            $totalPercentage += $percentage;
            $teamScore->setPercentage($percentage);
        }

        // Adjust the team with the highest points to ensure the total percentage is exactly 100%
        if ($totalPercentage !== Constants::HUNDRED_PERCENT && !empty($teamScores)) {
            usort(
                $teamScores,
                static function (CharacterQuizSubmissionTeamResult $a, CharacterQuizSubmissionTeamResult $b) {
                    return $a->getPoints() <=> $b->getPoints();
                }
            );
            $teamScores[0]->setPercentage(
                $teamScores[0]->getPercentage() + (Constants::HUNDRED_PERCENT - $totalPercentage)
            );
        }

        return $characterQuizSubmission;
    }
}