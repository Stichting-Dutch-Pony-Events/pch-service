<?php
namespace App\Util\Validator\Validation;

class CachedQuery {
    public function __construct(
        public string $query,
        public array $parameters,
        public mixed $result
    ) {}
}
