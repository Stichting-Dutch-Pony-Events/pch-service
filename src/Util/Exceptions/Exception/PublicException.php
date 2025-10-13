<?php

namespace App\Util\Exceptions\Exception;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;


#[OA\Schema(
    schema: "ErrorResponse",
    description: "Standard error response",
    properties: [
        new OA\Property(property: "status", type: "integer", example: 400),
        new OA\Property(property: "error", type: "string", example: "Bad Request"),
        new OA\Property(property: "message", type: "string", example: "Entity not persisted")
    ],
    type: "object"
)]
class PublicException extends Exception implements CustomExceptionInterface
{
    /** @var Collection<array-key, Parameter> $parameterCollection */
    public Collection $parameterCollection;

    /**
     * @param string $message
     * @param Collection<array-key, Parameter>|null $parameterCollection
     * @param int $code
     */
    public function __construct(
        string      $message = "",
        ?Collection $parameterCollection = null,
        int         $code = 0,
    ) {
        $this->parameterCollection = $parameterCollection ?? new ArrayCollection();

        parent::__construct($message, $code);
    }

    public function getHttpStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * @return array<array-key, Parameter>
     */
    public function getData(): array
    {
        return $this->parameterCollection->toArray();
    }

    final public function isPublicException(): bool
    {
        return true;
    }
}
