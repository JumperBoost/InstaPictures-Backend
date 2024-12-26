<?php
namespace App\Repository;

use Symfony\Component\Dotenv\Dotenv;

class Environment {
    private static ?Environment $instance = null;

    private Dotenv $dotenv;

    private function __construct() {
        $this->dotenv = new Dotenv();
        $this->dotenv->load(__DIR__ . '/../../.env');
    }

    public static function getInstance(): Environment {
        if(is_null(static::$instance))
            static::$instance = new Environment();
        return static::$instance;
    }

    public function getVariable(string $key): mixed {
        return $_ENV[$key];
    }
}