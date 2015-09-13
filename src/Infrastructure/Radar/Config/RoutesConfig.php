<?php

namespace Scheduler\Infrastructure\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Scheduler\Application\Service\GetShiftsInTimePeriod;
use Scheduler\Infrastructure\Radar\Responder\Responder;
use Scheduler\Infrastructure\Radar\Responder\ShiftResponder;
use Scheduler\REST\Resource\ShiftResource;
use Scheduler\REST\Resource\UserResource;

class RoutesConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        $di->set('shift.domain', $di->lazyNew(GetShiftsInTimePeriod::class));
        $di->params[GetShiftsInTimePeriod::class]["shiftMapper"] = $di->lazyGet("shift.mapper");

        $di->set("shift.responder", $di->lazyNew(ShiftResponder::class));
        $di->params[ShiftResponder::class]["resource"] = $di->lazyGet("shift.resource");

        $di->set("shift.resource", $di->lazyNew(ShiftResource::class));
        $di->params[ShiftResource::class]["userResource"] = $di->lazyGet("user.resource");

        $di->set("user.resource", $di->lazyNew(UserResource::class));
    }

    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        $adr->get('entry', "/", function ($user) {
            $payload = new \Aura\Payload\Payload();

            if (! $user->isAuthenticated()) {
                return $payload->setStatus($payload::NOT_AUTHENTICATED);
            }

            $payload->setStatus($payload::SUCCESS);
            $payload->setOutput([
                "links" => [
                    "shifts" => "/shifts"
                ]
            ]);

            return $payload;
        })
        ->input(function (Request $request) use ($di) {
            $token = $request->getHeaderLine("x-access-token");
            $user = $di->get("auth.authenticator")->getUserForToken($token);

            return [$user];
        })
        ->responder(Responder::class);

        $adr->get('get.shifts', "/shifts", GetShiftsInTimePeriod::class)
            ->input(function (Request $request) use ($di) {
                $token = $request->getHeaderLine("x-access-token");
                $user = $di->get("auth.authenticator")->getUserForToken($token);
                $start = $request->getQueryParams()["start"];
                $end = $request->getQueryParams()["end"];
                return [$user, $start, $end];
            })
            ->responder(ShiftResponder::class);
    }
}
