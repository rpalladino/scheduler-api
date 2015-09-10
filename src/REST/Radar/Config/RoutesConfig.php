<?php

namespace Scheduler\REST\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

class RoutesConfig extends ContainerConfig
{
    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        $adr->get('Hello', '/hello/{name}?', function (array $input) {
            extract($input);
            $payload = new \Aura\Payload\Payload();

            return $payload
                    ->setStatus($payload::SUCCESS)
                    ->setOutput("Hello $name");
        })->defaults(['name' => 'world']);
    }
}
