<?php

namespace Scheduler\Web\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Radar\Adr\Handler\ActionHandler;
use Radar\Adr\Handler\RoutingHandler;
use Relay\Middleware\JsonDecoder;
use Relay\Middleware\ResponseSender;
use Scheduler\Web\Radar\Middleware\ApiProblemExceptionHandler;
use Zend\Diactoros\Response;

class MiddlewareConfig extends ContainerConfig
{
    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        $adr->middle(new ResponseSender());
        $adr->middle(new ApiProblemExceptionHandler(new Response()));
        $adr->middle(new JsonDecoder());
        $adr->middle(RoutingHandler::class);
        $adr->middle(ActionHandler::class);
    }
}
