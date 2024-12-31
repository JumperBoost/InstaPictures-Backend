<?php
namespace App\Repository;

use App\Utils\PostUtil;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class CityRepository {
    private static ?CityRepository $instance = null;

    private string $index = "instapictures_cities";

    private function __construct() {}

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
        return ElasticClient::getInstance()->getClient()->search($params)->asArray();
    }

    public function search(string $value): array {
        $actorId = 'apify~instagram-scraper';
        $input = [
            "search" => $value,
            "searchLimit" => 5,
            "searchType" => "place"
        ];
        $options = ["timeout" => 90];
        $res = ApifyClient::getInstance()->runActorAndGetDatasetItems($actorId, $input, $options);

        $posts = [];
        if(!is_null($res)) {
            foreach($res as $loc) {
                if(isset($loc['location_id'])) {
                    $loc_id = $loc['location_id'];
                    $posts[$loc_id] = ["topPostsCount" => count($loc['topPosts']), "latestPostsCount" => count($loc['latestPosts'])];
                    foreach($loc['topPosts'] as $topPost)
                        $posts[$loc_id]["topPosts"][] = PostUtil::getInformationFromApiArray($topPost);
                    foreach($loc['latestPosts'] as $latestPost)
                        $posts[$loc_id]["latestPosts"][] = PostUtil::getInformationFromApiArray($latestPost);
                }
            }
        }
        return $posts;
    }
}