<?php

namespace App\Util\Exceptions\Exception\Entity;

use Exception;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        new OA\Property(property: "message", type: "string", example: "Entity not persisted"),
        new OA\Property(property: "code", type: "integer", example: 400)
    ],
    type: "object"
)]
class EntityNotPersistedException extends Exception
{
    public function __construct(string $message = 'Entity not persisted', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}