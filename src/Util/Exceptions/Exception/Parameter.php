<?php

namespace App\Util\Exceptions\Exception;

class Parameter
{
    /**
     * @param string $field
     * @param string|int $value
     */
    public function __construct(
        public string $field,
        public mixed  $value
    ) {
    }
}
