<?php

namespace Scheduler\Infrastructure\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Scheduler\Application\Service;
use Scheduler\Infrastructure\Radar\Input;
use Scheduler\Infrastructure\Radar\Responder;
use Scheduler\REST\Resource\ShiftResource;
use Scheduler\REST\Resource\UserResource;

class RoutesConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        $di->set('default:input', $di->lazyNew(Input\RootInput::class));
        $di->params[Input\Input::class]["authenticator"] = $di->lazyGet("auth.authenticator");

        $di->set('get/shifts:input', $di->lazyNew(Input\GetShiftsInput::class));
        $di->params[Input\GetShiftsInput::class]["authenticator"] = $di->lazyGet("auth.authenticator");

        $di->set('get/shifts:domain', $di->lazyNew(Service\GetShiftsInTimePeriod::class));
        $di->params[Service\GetShiftsInTimePeriod::class]["shiftMapper"] = $di->lazyGet("shift.mapper");

        $di->set("get/shifts:responder", $di->lazyNew(Responder\ShiftResponder::class));
        $di->params[Responder\ShiftResponder::class]["resource"] = $di->lazyGet("shift.resource");

        $di->set("shift.resource", $di->lazyNew(ShiftResource::class));
        $di->params[ShiftResource::class]["userResource"] = $di->lazyGet("user.resource");

        $di->set("user.resource", $di->lazyNew(UserResource::class));
    }

    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');
        $adr->input(Input\Input::class);
        $adr->responder(Responder\Responder::class);

        $adr->get('entry', "/", function ($user) {
            $payload = new \Aura\Payload\Payload();

            if (! $user->isAuthenticated()) {
                return $payload->setStatus($payload::NOT_AUTHENTICATED);
            }

            $payload->setStatus($payload::SUCCESS);
            $payload->setOutput([
                "links" => [
                    "get_shifts" => "/shifts"
                ]
            ]);

            return $payload;
        });

        $adr->get('get.shifts', "/shifts", Service\GetShiftsInTimePeriod::class)
            ->input(Input\GetShiftsInput::class)
            ->responder(Responder\ShiftResponder::class);
    }
}
