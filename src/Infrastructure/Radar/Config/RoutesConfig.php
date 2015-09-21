<?php

namespace Scheduler\Infrastructure\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Scheduler\Application\Service;
use Scheduler\Infrastructure\Radar\Input;
use Scheduler\Infrastructure\Radar\Responder;

class RoutesConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        $di->params[Input\Input::class]["authenticator"] = $di->lazyGet("auth.authenticator");
        $di->params[Input\GetEmployeeShiftsInput::class]["authenticator"] = $di->lazyGet("auth.authenticator");
        $di->params[Input\GetShiftsInput::class]["authenticator"] = $di->lazyGet("auth.authenticator");

        $di->params[Service\CreateShift::class]["shiftMapper"] = $di->lazyGet("shift.mapper");
        $di->params[Service\GetEmployee::class]["userMapper"] = $di->lazyGet("user.mapper");
        $di->params[Service\GetHoursWorkedInWeek::class]["calculator"] = $di->lazyGet("hours.calculator");
        $di->params[Service\GetShift::class]["shiftMapper"] = $di->lazyGet("shift.mapper");
        $di->params[Service\GetShiftsAssignedToEmployee::class]["shiftMapper"] = $di->lazyGet("shift.mapper");
        $di->params[Service\GetShiftsInTimePeriod::class]["shiftMapper"] = $di->lazyGet("shift.mapper");
        $di->params[Service\UpdateShift::class]["shiftMapper"] = $di->lazyGet("shift.mapper");
        $di->params[Service\UpdateShift::class]["userMapper"] = $di->lazyGet("user.mapper");

        $di->params[Responder\HoursWorkedSummaryResponder::class]["resource"] = $di->lazyGet("summary.resource");
        $di->params[Responder\ShiftResponder::class]["resource"] = $di->lazyGet("shift.resource");
        $di->params[Responder\ShiftItemResponder::class]["shiftResource"] = $di->lazyGet("shift.resource");
        $di->params[Responder\ShiftItemResponder::class]["userResource"] = $di->lazyGet("user.resource");
        $di->params[Responder\UserResponder::class]["resource"] = $di->lazyGet("user.resource");
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

        $adr->get('get.employee', '/employees/{id}', Service\GetEmployee::class)
            ->input(Input\GetEmployeeInput::class)
            ->responder(Responder\UserResponder::class);

        $adr->get('get.employee.shifts', "/employees/{id}/shifts", Service\GetShiftsAssignedToEmployee::class)
            ->input(Input\GetEmployeeInput::class)
            ->responder(Responder\ShiftResponder::class);

        $adr->get('get.employee.hours.weekly', "/employees/{id}/hours/weekly", Service\GetHoursWorkedInWeek::class)
            ->input(Input\GetEmployeeHoursWeeklyInput::class)
            ->responder(Responder\HoursWorkedSummaryResponder::class);

        $adr->get('get.shifts', "/shifts", Service\GetShiftsInTimePeriod::class)
            ->input(Input\GetShiftsInput::class)
            ->responder(Responder\ShiftResponder::class);

        $adr->post('post.shifts', "/shifts", Service\CreateShift::class)
            ->input(Input\CreateShiftInput::class)
            ->responder(Responder\ShiftResponder::class);

        $adr->get('get.shift', "/shifts/{id}", Service\GetShift::class)
            ->input(Input\GetShiftInput::class)
            ->responder(Responder\ShiftItemResponder::class);

        $adr->put('put.shifts', "/shifts/{id}", Service\UpdateShift::class)
            ->input(Input\UpdateShiftInput::class)
            ->responder(Responder\ShiftResponder::class);
    }
}
