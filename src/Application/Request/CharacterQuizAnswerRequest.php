<?php

namespace App\Application\Request;

class CharacterQuizAnswerRequest
{
    public function __construct(
        public string $questionId,
        public string $answerId
    ) {
    }
}