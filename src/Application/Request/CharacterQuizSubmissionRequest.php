<?php

namespace App\Application\Request;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class CharacterQuizSubmissionRequest
{
    /**
     * @param CharacterQuizAnswerRequest[] $answers
     */
    public function __construct(
        #[OA\Property(
            type: "array",
            items: new OA\Items(ref: new Model(type: CharacterQuizAnswerRequest::class))
        )]
        public array $answers
    ) {
    }
}