<?php

namespace App\Service;

use App\Exception\CMFBanksApiException;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CMFBanksApiService
{
    private const string DOLLARS = 'Dolares';
    private const string URL = "https://api.sbif.cl/api-sbifv3/recursos_api/dolar";
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client, string $sbfiApiKey)
    {
        $this->client = $client;
        $this->apiKey = $sbfiApiKey;
    }

    public function dollarExchange(string $year, string $month): array
    {
        try {
            $response = $this->client->request(
                'GET',
                self::URL . "/$year/$month",
                [
                    'query' => [
                        'apikey' => $this->apiKey,
                        'formato' => 'json'
                    ]
                ]
            );

            $data = $response->toArray();

            if (!isset($data[self::DOLLARS])) {
                return [];
            }

            return array_map(function ($item) {
                return [
                    'fecha' => $item['Fecha'],
                    'valor' => $item['Valor']
                ];
            }, $data[self::DOLLARS]);
        } catch (
            ClientExceptionInterface|
            DecodingExceptionInterface|
            RedirectionExceptionInterface|
            ServerExceptionInterface|
            TransportExceptionInterface $e
        ) {
            throw new CMFBanksApiException('Error al obtener los datos de la API: ' . $e->getMessage());
        }
    }
}
