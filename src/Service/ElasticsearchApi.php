<?php

namespace App\Service;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Exception;

class ElasticsearchApi {

    private $api;
    private $cloud;

    public function __construct(string $apikeys, string $cloudid)
    {
        $this->api = $apikeys;
        $this->cloud = $cloudid;
    }

    public function ElasticApi(){

        /*------ API SETTINGS ------*/

        $apiKey = $this->api;
        $cloudId = $this->cloud;

        $client = ClientBuilder::create()
        ->setElasticCloudId($cloudId)
        ->setApiKey($apiKey)
        ->build();

        /*------ PREPARING RESEARCH ------*/

        $params = [
            'index' => 'research',
            'body'  => [ 'testField' => 'abc']
        ];

        try {
            $response = $client->index($params);
        } catch (ClientResponseException $e) {
            //manage 40X errors
        } catch (ServerResponseException $e) {
            // manage 50X errors
        } catch (Exception $e) {
            // catch network error
        }

        print_r($response->asArray());

        //  The ID of the document is created automatically and stored in $response['_id']

        $params = [
            'index' => 'research',
            'body'  => [
                'query' => [
                    'match' => [
                        'testField' => 'abc'
                    ]
                ]
            ]
        ];
        
        $response = $client->search($params);
        
        printf("Total docs: %d\n", $response['hits']['total']['value']);
        printf("Max score : %.4f\n", $response['hits']['max_score']);
        printf("Took      : %d ms\n", $response['took']);
        
        print_r($response['hits']['hits']); // documents
    }
}