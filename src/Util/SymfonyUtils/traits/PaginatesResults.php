<?php

namespace App\Util\SymfonyUtils\traits;

use App\Util\SymfonyUtils\Mapper;
use App\Util\SymfonyUtils\Views\CollectionView;
use App\Util\SymfonyUtils\Views\PaginationOptions;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use ReflectionException;

trait PaginatesResults
{
    /**
     * @param PaginationOptions $paginationOptions
     * @param $query
     * @param $view
     *
     * @return CollectionView
     * @throws ReflectionException
     * @throws Exception
     */
    public function paginate(PaginationOptions $paginationOptions, $query, $view = null): CollectionView
    {
        $offset = $paginationOptions->offset ?: $paginationOptions->limit * ($paginationOptions->page - 1);

        $paginator = new Paginator(
            $query->setMaxResults($paginationOptions->limit)->setFirstResult($offset)
        );

        $data = $view === null ?
            (array)$paginator->getIterator() :
            Mapper::mapMany((array)$paginator->getIterator(), $view);
        $meta = [
            'total' => count($paginator),
        ];

        return new CollectionView($data, $meta);
    }
}
