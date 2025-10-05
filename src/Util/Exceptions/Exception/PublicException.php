<?php

namespace App\Util\Exceptions\Exception;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PublicException extends Exception implements CustomExceptionInterface
{
    /** @var Collection<array-key, Parameter> $parameterCollection */
    public Collection $parameterCollection;

    /**
     * @param string $message
     * @param Collection<array-key, Parameter>|null $parameterCollection
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string         $message = "",
        ?Collection    $parameterCollection = null,
        int            $code = 0,
        Throwable|null $previous = null
    ) {
        $this->parameterCollection = $parameterCollection ?? new ArrayCollection();

        parent::__construct($message, $code, $previous);
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
