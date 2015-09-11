<?php

namespace Scheduler\REST\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Scheduler\Application\Service\GetShiftsInTimePeriod;
use Scheduler\REST\Radar\Responder\ShiftResponder;
use Scheduler\REST\Resource\ShiftResource;

class RoutesConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        $di->set('shift.domain', $di->lazyNew(GetShiftsInTimePeriod::class));
        $di->params[GetShiftsInTimePeriod::class]["shiftMapper"] = $di->lazyGet("shift.mapper");

        $di->set("shift.responder", $di->lazyNew(ShiftResponder::class));
        $di->params[ShiftResponder::class]["resource"] = $di->lazyGet("shift.resource");

        $di->set("shift.resource", $di->lazyNew(ShiftResource::class));
    }

    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        $adr->get('get.shifts', "/shifts", GetShiftsInTimePeriod::class)
            ->input(function (Request $request) {
                $queryParams = $request->getQueryParams();
                return [
                    new \DateTimeImmutable(urldecode($queryParams["start"])),
                    new \DateTimeImmutable(urldecode($queryParams["end"]))
                ];
            })
            ->responder(ShiftResponder::class);
    }
}
