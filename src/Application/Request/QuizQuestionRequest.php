<?php

namespace App\Application\Request;

use JMS\Serializer\Annotation\Type;

class QuizQuestionRequest
{
    public function __construct(
        public string $question,

        /** @var QuizAnswerRequest[] $answers */
        #[Type('array<' . QuizAnswerRequest::class . '>')]
        public array  $answers
    ) {
    }
}