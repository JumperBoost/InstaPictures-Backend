<?php
namespace App\Repository;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class CityRepository {
    private static ?CityRepository $instance = null;

    private Client $client;

    private string $index = "instapictures_cities";
    private string $nom_field = "nom";

    private function __construct() {
        $this->client = ElasticClient::getInstance()->getClient();
    }

    public static function getInstance(): CityRepository {
        if(is_null(static::$instance))
            static::$instance = new CityRepository();
        return static::$instance;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(string $value): array {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'query_string' => [
                        'default_field' => $this->nom_field,
                        'query' => htmlspecialchars($value) . "*"
                    ]
                ],
                'size' => 10
            ]
        ];
        return $this->client->search($params)->asArray();
    }
}