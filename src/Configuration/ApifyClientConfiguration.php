<?php
namespace App\Configuration;

use App\Repository\Environment;

class ApifyClientConfiguration {
    public static function getApiToken(): string {
        return Environment::getInstance()->getVariable("APIFY_API_TOKEN");
    }
}