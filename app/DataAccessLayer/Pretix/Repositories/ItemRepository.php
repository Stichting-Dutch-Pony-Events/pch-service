<?php

namespace App\DataAccessLayer\Pretix\Repositories;

use App\DataAccessLayer\Pretix\Views\Item;

class ItemRepository extends PretixBaseRepository
{
    public function getItemById(int $id): Item
    {
        return new Item(json_decode($this->getClient()->get('items/'.$id)->getBody()));
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        $items   = $this->retrieveAll('items');
        $itemObj = [];
        foreach ($items as $item) {
            $itemObj[] = new Item($item);
        }
        return $itemObj;
    }
}
