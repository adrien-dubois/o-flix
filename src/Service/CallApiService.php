<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
            $this->client = $client;
    }

    public function getData()
    {

        $response = $this->client->request(
            'GET',
            'https://api.themoviedb.org/3/tv/71446?api_key=e7fad1d70c5920679b724aee18680057&language=fr-FR'
        );

        return $response->toArray();
    }
}