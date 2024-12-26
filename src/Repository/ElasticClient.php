<?php
namespace App\Repository;

use App\Configuration\ElasticClientConfiguration;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class ElasticClient {
    private static ?ElasticClient $instance = null;

    private Client $client;

    /**
     * @throws AuthenticationException
     */
    private function __construct() {
        $this->client = ClientBuilder::create()
            ->setHosts([ElasticClientConfiguration::getUrl()])
            ->setBasicAuthentication(ElasticClientConfiguration::getUser(), ElasticClientConfiguration::getPassword())
            ->build();
    }

    public static function getInstance(): ElasticClient {
        if(is_null(static::$instance))
            static::$instance = new ElasticClient();
        return static::$instance;
    }

    public function getClient(): Client {
        return $this->client;
    }
}