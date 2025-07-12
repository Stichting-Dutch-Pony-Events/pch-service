<?php

namespace App\Application\Request;

class QuizQuestionRequest
{
    public function __construct(
        public string $title,
        public string $question,
    ) {
    }
}