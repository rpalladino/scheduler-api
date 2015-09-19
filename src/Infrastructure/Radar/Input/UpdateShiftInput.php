<?php

namespace Scheduler\Infrastructure\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateShiftInput extends Input
{
    public function __invoke(Request $request)
    {
        $currentUser = $this->getCurrentUser($request);
        $shiftId = (int) $request->getAttribute('id');
        $body = $request->getParsedBody();
        $employeeId = isset($body->employee_id) ? (int) $body->employee_id : 0;

        return [$currentUser, $shiftId, $employeeId];
    }
}
