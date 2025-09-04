<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CheapsharkApi
{
    //-- Attributes --\\
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

    //-- Methods --\\
    public function __construct(HttpClientInterface $c)
    {
        $this->client = $c;
    }

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

    public function getFrom(string $field, array $options): array
    {
        $response = $this->client->request('GET', $this->baseUrl . $field, ['query' => $options]);
        return $response->toArray();
    }
}
