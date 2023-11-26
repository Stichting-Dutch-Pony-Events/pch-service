<?php

namespace App\Util\SymfonyUtils\traits;

use App\Util\SymfonyUtils\Views\PaginationOptions;
use Symfony\Component\HttpFoundation\Request;

trait RetrievesPaginationOptions
{
    /**
     * @param Request $request
     *
     * @return PaginationOptions
     */
    public function getPaginationOptions(Request $request): PaginationOptions
    {
        return new PaginationOptions(
            (int)$request->get('page', 1),
            (int)$request->get('limit', 10),
            (int)$request->get('offset')
        );
    }
}
