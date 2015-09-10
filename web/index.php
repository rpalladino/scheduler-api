<?php

require __DIR__ . "/../vendor/autoload.php";

use Radar\Adr\Boot;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

$boot = new Boot();
$adr = $boot->adr([
    Scheduler\REST\Radar\Config\MiddlewareConfig::class,
    Scheduler\REST\Radar\Config\RoutesConfig::class
]);

$adr->run(ServerRequestFactory::fromGlobals(), new Response());
