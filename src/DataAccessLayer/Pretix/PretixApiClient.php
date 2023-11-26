<?php

namespace App\DataAccessLayer\Pretix;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PretixApiClient
{
    public function __construct(
        private HttpClientInterface $client,
        string                      $baseUrl,
        string                      $apiKey,
        string                      $organiser,
        string                      $event
    ) {
        if (!str_ends_with($baseUrl, '/')) {
            $baseUrl .= '/';
        }
        $baseUrl .= 'api/v1/organizers/'.$organiser.'/events/'.$event.'/';

        $this->client = $this->client->withOptions([
            'base_uri' => $baseUrl,
            'headers'  => [
                'Authorization' => 'Token '.$apiKey,
                'Accept'        => 'application/json'
            ]
        ]);
    }

    public function addParametersToUrl(string $url, array $parameters): string
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

    public function retrieveAll($uri, $parameters = []): array
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
            $objects        = json_decode($this->client->request(Request::METHOD_GET, $url)->getContent());
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

    public function retrieve(string $uri): object {
        return json_decode($this->client->request(Request::METHOD_GET, $uri)->getContent());
    }
}