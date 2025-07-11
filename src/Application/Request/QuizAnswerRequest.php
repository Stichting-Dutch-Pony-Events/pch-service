<?php

namespace App\Application\Request;

use JMS\Serializer\Annotation\Type;

class QuizAnswerRequest
{
    public function __construct(
        public ?string $id,
        public string  $title,
        public string  $answer,

        /** @var QuizAnswerTeamWeightRequest[] $teamWeights */
        #[Type('array<' . QuizAnswerTeamWeightRequest::class . '>')]
        public array   $teamWeights = []
    ) {
    }
}