<?php

namespace Scheduler\Infrastructure\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;

class GetEmployeeShiftsInput extends Input
{
    public function __invoke(Request $request)
    {
        $currentUser = $this->getCurrentUser($request);
        $employeeId = (int) $request->getAttribute('id');

        return [$currentUser, $employeeId];
    }
}
