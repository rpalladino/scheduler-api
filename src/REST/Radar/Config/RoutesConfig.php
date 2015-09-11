<?php

namespace Scheduler\REST\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Scheduler\Application\Service\GetShiftsInTimePeriod;
use Scheduler\Domain\Model\Shift\ShiftMapper;

class RoutesConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        $di->set('GetShiftsInTimePeriod', $di->lazyNew(GetShiftsInTimePeriod::class));
        $di->params[GetShiftsInTimePeriod::class]["shiftMapper"] = $di->lazyGet(ShiftMapper::class);
    }

    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        $adr->get('GetShiftsInTimePeriod', "/shifts", GetShiftsInTimePeriod::class)
            ->input(function (Request $request) {
                $queryParams = $request->getQueryParams();
                return [
                    new \DateTimeImmutable(urldecode($queryParams["start"])),
                    new \DateTimeImmutable(urldecode($queryParams["end"]))
                ];
            });
    }
}
