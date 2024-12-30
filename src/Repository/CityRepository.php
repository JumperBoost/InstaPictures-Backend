<?php
namespace App\Repository;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class CityRepository {
    private static ?CityRepository $instance = null;

    private Client $client;

    private string $index = "instapictures_cities";
    private string $nom_field = "name";

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
    public function autocomplete(string $value): array {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'multi_match' => [
                                    'query' => htmlspecialchars($value),
                                    'fields' => ['name^2', 'postal_code^3', 'department', 'region', 'country^2'],
                                    'fuzziness' => 'AUTO',
                                    'operator' => 'or'
                                ]
                            ],
                            [
                                'match' => [
                                    'name' => [
                                        'query' => htmlspecialchars($value),
                                        "boost" => 25
                                    ]
                                ]
                            ],
                            [
                                'match' => [
                                    'postal_code' => [
                                        'query' => htmlspecialchars($value),
                                        "boost" => 30
                                    ]
                                ]
                            ]
                        ],
                        // Ajout d'une clause fallback pour garantir un résultat si aucune correspondance stricte n'est trouvée
                        'minimum_should_match' => 1
                    ]
                ],
                'size' => 10
            ]
        ];
        return $this->client->search($params)->asArray();
    }
}