<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbApi{


    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Method to seach a tv show  by his title
     *
     * @param [type] $title
     * @return void
     */
    public function fetch(string $title)
    {
        $response = $this->client->request(
            'GET',
            'http://www.omdbapi.com/?i=tt3896198&apikey=1a31398b&t=' . $title
        );

        return $response->toArray();
    }
}