<?php
namespace App\Configuration;

use App\Repository\Environment;

class ElasticClientConfiguration {
    public static function getUrl(): string {
        return Environment::getInstance()->getVariable("ELASTIC_URL");
    }

    public static function getUser(): string {
        return Environment::getInstance()->getVariable("ELASTIC_USER");
    }

    public static function getPassword(): string {
        return Environment::getInstance()->getVariable("ELASTIC_PASSWORD");
    }
}