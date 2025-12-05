<?php

use App\Services\EncryptionService;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

require_once __DIR__ . '/../app/helpers.php';

$router = new App\Core\Router();
require_once __DIR__ . '/../config/routes.php';

$router->dispatch(); 

