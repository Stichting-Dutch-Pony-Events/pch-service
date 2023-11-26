<?php

namespace App\Util\SymfonyUtils\Attribute;

use Attribute;

#[Attribute]
class MapsMany
{
    public ?string $viewClass;
    public bool $useTypeResolver;
    public ?string $supportedTypeResolverInterface = null;

    public function __construct(
        ?string $viewClass = null,
        bool $useTypeResolver = false,
        ?string $supportedTypeResolverInterface = null,
    )
    {
        $this->viewClass = $viewClass;
        $this->useTypeResolver = $useTypeResolver;
        $this->supportedTypeResolverInterface = $supportedTypeResolverInterface;
    }
}
