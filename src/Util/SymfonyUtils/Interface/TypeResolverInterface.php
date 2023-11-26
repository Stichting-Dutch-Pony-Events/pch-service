<?php

namespace App\Util\SymfonyUtils\Interface;

// Constructor not allowed
interface TypeResolverInterface
{
    public function determineType(mixed $value): string;

    public function supports(mixed $value): bool;
}
