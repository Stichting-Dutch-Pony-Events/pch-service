<?php

namespace App\Application\Request;

use JMS\Serializer\Annotation\Type;

class QuizQuestionRequest
{
    public function __construct(
        public string $question,
    ) {
    }
}