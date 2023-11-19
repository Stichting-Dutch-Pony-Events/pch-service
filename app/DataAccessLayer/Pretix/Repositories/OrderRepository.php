<?php

namespace App\DataAccessLayer\Pretix\Repositories;

use App\DataAccessLayer\Pretix\Views\Order;

class OrderRepository extends PretixBaseRepository
{
    public function getOrderByCode(string $orderCode): Order
    {
        $uri = "orders/".$orderCode;
        return new Order(json_decode($this->getClient()->get($uri)->getBody()));
    }

    public function getOrders()
    {
        $orders   = $this->retrieveAll('orders', ['status' => 'p']);
        $orderObj = [];
        foreach ($orders as $order) {
            $orderObj[] = new Order($order);
        }
        return $orderObj;
    }
}
