<?php

namespace App\DataAccessLayer\Pretix\Repositories;

use GuzzleHttp\Client;

class PretixBaseRepository
{
    private Client $client;

    protected function getClient(): Client
    {
        if (isset($this->client)) {
            return $this->client;
        }

        $organiser = config('pretix.organiser');
        $event     = config('pretix.event');

        $baseUrl = config('pretix.url');
        if (!str_ends_with($baseUrl, '/')) {
            $baseUrl .= '/';
        }
        $baseUrl .= 'api/v1/organizers/'.$organiser.'/events/'.$event.'/';

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'headers'  => [
                'Authorization' => 'Token '.config('pretix.api_key'),
                'Accept'        => 'application/json'
            ]
        ]);

        return $this->client;
    }

    protected function addParametersToUrl(string $url, array $parameters): string
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

    protected function retrieveAll($uri, $parameters = [])
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
            $objects        = json_decode($this->getClient()->get($url)->getBody());
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
