<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CheapsharkApi
{
    private HttpClientInterface $client;
    private $baseUrl = 'https://www.cheapshark.com/api/1.0/';

    public function __construct(HttpClientInterface $c) {
        $this->client = $c;
    }
    
    public function getGameLookup(int $id): array {
        $response = $this->client->request('GET', $this->baseUrl . 'games', ['query' => ['id' => $id]]);

        return $response->toArray();
    }

    public function getDeals(): array {
        $response = $this->client->request('GET', $this->baseUrl . 'deals?pageSize=10&sortBy=Savings');

        return $response->toArray();
    }

}