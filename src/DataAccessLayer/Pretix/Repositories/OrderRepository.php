<?php

namespace App\DataAccessLayer\Pretix\Repositories;

use App\DataAccessLayer\Pretix\Views\Order;

class OrderRepository extends PretixBaseRepository
{
    public function getOrderByCode(string $orderCode): Order
    {
        $uri = "orders/".$orderCode;
        return new Order($this->pretixApiClient->retrieve($uri));
    }

    public function getOrders()
    {
        $orders   = $this->pretixApiClient->retrieveAll('orders', ['status' => 'p']);
        $orderObj = [];
        foreach ($orders as $order) {
            $orderObj[] = new Order($order);
        }
        return $orderObj;
    }
}
