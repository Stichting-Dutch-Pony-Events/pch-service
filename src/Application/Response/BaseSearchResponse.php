<?php

namespace App\Application\Response;

class BaseSearchResponse
{
    public array $items;
    public int $total;
    public int $page;
    public int $itemsPerPage;
}