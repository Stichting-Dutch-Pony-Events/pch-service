<?php

namespace App\Application\Request;

use JMS\Serializer\Annotation\Type;

class QuizAnswerRequest
{
    public function __construct(
        public ?string $id,
        public string  $answer,
        public int     $order,

        /** @var QuizAnswerTeamWeightRequest[] $teamWeights */
        #[Type('array<' . QuizAnswerTeamWeightRequest::class . '>')]
        public array   $teamWeights = []
    ) {
    }
}