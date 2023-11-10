<?php

namespace DataAccessLayer\Pretix;

use DataAccessLayer\Pretix\Views\CheckInList;
use DataAccessLayer\Pretix\Views\Invoice;
use DataAccessLayer\Pretix\Views\Item;
use DataAccessLayer\Pretix\Views\Order;
use GuzzleHttp\Client;

use function PHPUnit\Framework\stringContains;

class PretixApi
{
    /** @var Client $client */
    public Client $client;

    public function __construct(string $organiser = null, string $event = null)
    {
        if ($organiser === "" || $organiser === null) {
            $organiser = config('pretix.organiser');
        }

        if ($event === "" || $event === null) {
            $event = config('pretix.event');
        }

        $baseUrl = config('pretix.url');
        if (!str_ends_with($baseUrl, '/')) {
            $baseUrl .= '/';
        }

        $baseUrl      .= 'api/v1/organizers/'.$organiser.'/events/'.$event.'/';

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'headers'  => [
                'Authorization' => 'Token '.config('pretix.api_key'),
                'Accept'        => 'application/json'
            ]
        ]);
    }

    /**
     * @return CheckInList[]
     */
    public function getCheckinLists(): array {
        $checkinLists = $this->retrieveAll('checkinlists');

        $checkinListObjs = [];
        foreach ($checkinLists as $checkinList) {
            $checkinListObjs[] = new CheckInList($checkinList);
        }

        return $checkinListObjs;
    }

    public function getInvoices(string $order = null)
    {
        $parameters = [];
        if ($order !== null) {
            $parameters['order'] = $order;
        }
        $invoices   = $this->retrieveAll("invoices", $parameters);
        $invObjects = [];
        foreach ($invoices as $inv) {
            $invObjects[] = new Invoice($inv);
        }
        return $invObjects;
    }

    public function getOrder(string $order): Order
    {
        $uri = "orders/".$order;
        return new Order(json_decode($this->client->get($uri)->getBody()));
    }

    public function getItem(int $item): ?Item
    {
        return new Item(json_decode($this->client->get('items/'.$item)->getBody()));
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


    private function addParametersToUrl(string $url, array $parameters): string
    {
        $i = 0;
        foreach ($parameters as $key => $value) {
            if ($i === 0) {
                $url .= '?';
            } else {
                $url .= '&';
            }
            $url .= $key.'='.$value;
            $i++;
        }
        return $url;
    }

    private function retrieveAll($uri, $parameters = [])
    {
        if ($parameters === null) {
            $parameters = [];
        }
        $parameters['page'] = 1;
        $nextPageExists     = true;
        $results            = [];
        while ($nextPageExists) {
            $nextPageExists = false;
            $url            = $this->addParametersToUrl($uri, $parameters);
            $objects        = json_decode($this->client->get($url)->getBody());
            if (property_exists($objects, 'next') && $objects->next !== null) {
                $parameters['page']++;
                $nextPageExists = true;
            }
            if (property_exists($objects, 'results') && is_array($objects->results)) {
                $results = array_merge($results, $objects->results);
            }
        }
        return $results;
    }
}
