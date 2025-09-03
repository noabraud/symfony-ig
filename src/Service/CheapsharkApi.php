<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CheapsharkApi
{
    private HttpClientInterface $client;
    private $baseUrl = 'https://www.cheapshark.com/api/1.0/';

    private $storesID = [
        'Steam' => 1,
        'GOG' => 7,
        'Origin' => 8,
        'Uplay' => 13,
        'Epic Games' => 25,
        'Blizzard Shop' => 31
    ];

    public function getStoresID(array $storesName = []): array
    {
        if ($storesName && count($storesName) > 0) {
            $result = [];

            foreach ($storesName as $store) {
                if (isset($this->storesID[$store])) {
                    $result[$store] = $this->storesID[$store];
                }
            }
            return $result;
        }
        return $this->storesID;
    }

    public function filterDealsByAllowedStores(array $deals, array $stores): array
    {
        $allowedStores = array_flip($stores);
        return array_filter($deals, fn($deal) => isset($allowedStores[$deal['storeID']]));
    }

    public function __construct(HttpClientInterface $c)
    {
        $this->client = $c;
    }

    public function getFrom(string $field, array $options): array
    {
        $response = $this->client->request('GET', $this->baseUrl . $field, ['query' => $options]);
        return $response->toArray();
    }



    // public function getGameLookup(int $id): array {
    //     $response = $this->client->request('GET', $this->baseUrl . 'games', ['query' => ['id' => $id]]);

    //     return $response->toArray();
    // }

    // public function getDeals(): array {
    //     $response = $this->client->request('GET', $this->baseUrl . 'deals?storeID=1,7,8,13,25,31&pageSize=10&sortBy=Savings');

    //     return $response->toArray();
    // }

    // public function getGamesList(string $title): array {
    //     $response = $this->client->request('GET', $this->baseUrl . 'deals', ['query' => ['title' => $title]]);

    //     return $response->toArray();
    // }

}
