<?php

require __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use Radar\Adr\Boot;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

$dotenvPath = __DIR__ . "/..";
if (file_exists($dotenvPath . "/.env")) {
    $dotenv = new Dotenv($dotenvPath);
    $dotenv->load();
}


$boot = new Boot();
$adr = $boot->adr([
    Scheduler\Web\Radar\Config\ServiceConfig::class,
    Scheduler\Web\Radar\Config\MiddlewareConfig::class,
    Scheduler\Web\Radar\Config\RoutesConfig::class,
    Scheduler\Web\Radar\Config\SeedConfig::class
]);

$adr->run(ServerRequestFactory::fromGlobals(), new Response());
