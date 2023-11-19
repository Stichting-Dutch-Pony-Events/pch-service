<?php

namespace App\DataAccessLayer\Pretix;

use GuzzleHttp\Client;

use function DataAccessLayer\Pretix\str_ends_with;

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
