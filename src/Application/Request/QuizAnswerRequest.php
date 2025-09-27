<?php

namespace App\Application\Request;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class QuizAnswerRequest
{
    /**
     * @param string $title
     * @param string $answer
     * @param QuizAnswerTeamWeightRequest[] $teamWeights
     */
    public function __construct(
        public string $title,
        public string $answer,

        #[OA\Property(
            type: "array",
            items: new OA\Items(ref: new Model(type: QuizAnswerTeamWeightRequest::class))
        )]
        public array  $teamWeights = []
    ) {
    }
}