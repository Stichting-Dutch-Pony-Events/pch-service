<?php

namespace App\Application\Request;

use JMS\Serializer\Annotation\Type;

class ChangeOrderRequest
{
    /**
     * @param  string[]  $ids
     */
    public function __construct(
        #[Type('array<string>')]
        public array $ids
    ) {
    }
}