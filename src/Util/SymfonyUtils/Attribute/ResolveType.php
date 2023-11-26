<?php

namespace App\Util\SymfonyUtils\Attribute;

use Attribute;

#[Attribute]
class ResolveType
{
    public function __construct(
        public ?string $supportedTypeResolverInterface = null
    )
    {
    }
}
