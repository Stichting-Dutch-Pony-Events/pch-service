<?php

namespace App\Application\Request;

class DispatchPrintJobRequest
{
    public function __construct(
        public string $attendeeIdentifier,
    ) {
    }
}