<?php
namespace App\Repository;


use App\Configuration\ApifyClientConfiguration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApifyClient {
    private static ?ApifyClient $instance = null;

    private Client $client;

    private function __construct() {
        $this->client = new Client([
            'base_uri' => 'https://api.apify.com',
            'timeout' => 100.0,
            'headers' => [
                'Authorization' => 'Bearer ' . ApifyClientConfiguration::getApiToken(),
            ]
        ]);
    }

    public static function getInstance(): ApifyClient {
        if(is_null(static::$instance))
            static::$instance = new ApifyClient();
        return self::$instance;
    }

    public function runActorAndGetDatasetItems(string $actorId, array $input = [], array $options = []): ?array {
        try {
            $res = $this->client->post(sprintf('v2/acts/%s/run-sync-get-dataset-items', $actorId), [
                'json' => $input,
                'query' => $options
            ]);
            return json_decode($res->getBody(), true);
        } catch (GuzzleException $e) {
            return null;
        }
    }
}