<?php

namespace App\Application\Request;

class LiveQuizCreateQuestionRequest
{
    /**
     * @param array<mixed> $options Raw JSON values; the service checks they're non-empty strings.
     */
    public function __construct(
        public ?string $label = null,
        public int $durationSeconds = 15,
        public array $options = [],
    ) {
    }
}
