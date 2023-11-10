<?php

namespace DataAccessLayer\Pretix\Views;

class Item
{
    public int $id;
    public int $category;
    public object $name;
    public ?object $metaData;

    public function __construct($item)
    {
        $this->id = $item->id;
        $this->category = $item->category;
        $this->name = $item->name;
        $this->metaData = $item->meta_data;
    }
}
