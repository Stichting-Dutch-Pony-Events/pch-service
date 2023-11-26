<?php


namespace App\Util\SymfonyUtils\Views;


/**
 * @template T
 */
class CollectionView
{
    /** @var array<int, T> */
    public array $data = [];

    public array $meta = [
        'total' => 0,
    ];

    public function __construct(array $data, array $meta)
    {
        $this->data = $data;
        $this->meta = $meta;
    }
}
