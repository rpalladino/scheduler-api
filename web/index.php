<?php

require __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use Radar\Adr\Boot;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

$dotenv = new Dotenv(__DIR__ . "/../");
$dotenv->load();

$boot = new Boot();
$adr = $boot->adr([
    Scheduler\Infrastructure\Radar\Config\ServiceConfig::class,
    Scheduler\Infrastructure\Radar\Config\MiddlewareConfig::class,
    Scheduler\Infrastructure\Radar\Config\RoutesConfig::class
]);

$adr->run(ServerRequestFactory::fromGlobals(), new Response());
