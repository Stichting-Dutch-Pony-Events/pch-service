<?php

namespace App\Application\View;

use App\Util\SymfonyUtils\Attribute\MapsMany;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class CharacterQuizSubmissionView
{
    /**
     * @param CharacterQuizSubmissionTeamResultView[] $teamResults
     */
    public function __construct(
        #[OA\Property(
            type: "array",
            items: new OA\Items(ref: new Model(type: CharacterQuizSubmissionTeamResultView::class))
        )]
        #[MapsMany(CharacterQuizSubmissionTeamResultView::class)]
        public array $teamResults
    ) {
    }
}