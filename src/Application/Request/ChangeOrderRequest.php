<?php

namespace App\Application\Request;

use OpenApi\Attributes as OA;

class ChangeOrderRequest
{
    /**
     * @param string[] $ids
     */
    public function __construct(
        #[OA\Property(
            type: "array",
            items: new OA\Items(type: "string", format: "uuid"),
            nullable: true
        )]
        public array $ids
    ) {
    }
}