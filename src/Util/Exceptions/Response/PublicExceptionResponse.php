<?php

namespace App\Util\Exceptions\Response;

use App\Util\Exceptions\Exception\Parameter;

class PublicExceptionResponse
{
    /**
     * @param string $message
     * @param Parameter[] $parameters
     */
    public function __construct(
        public string $message,
        public array  $parameters
    ) {
    }
}
