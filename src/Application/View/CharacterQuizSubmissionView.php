<?php

namespace App\Application\View;

use App\Util\SymfonyUtils\Attribute\MapsMany;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class CharacterQuizSubmissionView
{
    /**
     * @param bool $lastSubmission
     * @param CharacterQuizSubmissionTeamResultView[] $teamResults
     */
    public function __construct(
        public bool  $lastSubmission,
        #[OA\Property(
            type: "array",
            items: new OA\Items(ref: new Model(type: QuizAnswerTeamWeightView::class))
        )]
        #[MapsMany(QuizAnswerTeamWeightView::class)]
        public array $teamResults
    ) {
    }
}