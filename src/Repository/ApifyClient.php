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

    /** @return ?array Tableau de données de l'acteur */
    public function runActor(string $actorId, array $input = [], array $options = []): ?array {
        try {
            $res = $this->client->post(sprintf('v2/acts/%s/runs', $actorId), [
                'json' => $input,
                'query' => $options
            ]);
            $data = json_decode($res->getBody(), true);
            return $data['data'];
        } catch (GuzzleException $e) {
            return null;
        }
    }

    /**
     * @throws GuzzleException
     */
    public function waitUntilActorFinished(array $actorData, int $pauseInSeconds = 3): void {
        do {
            sleep($pauseInSeconds);
            $res = $this->client->get(sprintf('v2/actor-runs/%s', $actorData['id']));
            $data = json_decode($res->getBody(), true);
            $status = $data['data']['status'];
        } while(in_array($status, ['RUNNING', 'READY']));
    }

    public function getActorDatasetItems(array $actorData): ?array {
        try {
            $res = $this->client->get(sprintf('v2/datasets/%s/items', $actorData['defaultDatasetId']), [
                'query' => ['format' => 'json']
            ]);
            return json_decode($res->getBody(), true);
        } catch (GuzzleException $e) {
            return null;
        }
    }
}