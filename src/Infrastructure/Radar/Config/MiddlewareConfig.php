<?php

namespace Scheduler\Infrastructure\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Radar\Adr\Handler\ActionHandler;
use Radar\Adr\Handler\RoutingHandler;
use Relay\Middleware\JsonDecoder;
use Relay\Middleware\ResponseSender;
use Scheduler\Infrastructure\Radar\Middleware\ExceptionHandler;
use Zend\Diactoros\Response;

class MiddlewareConfig extends ContainerConfig
{
    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        $adr->middle(new ResponseSender());
        $adr->middle(new ExceptionHandler(new Response()));
        $adr->middle(new JsonDecoder());
        $adr->middle(RoutingHandler::class);
        $adr->middle(ActionHandler::class);
    }
}
