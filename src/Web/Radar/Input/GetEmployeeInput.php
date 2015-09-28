<?php

namespace Scheduler\Web\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;

class GetEmployeeInput extends Input
{

    public function __invoke(Request $request)
    {
        $user = $this->getCurrentUser($request);
        $employeeId = (int) $request->getAttribute('id');

        return [$user, $employeeId];
    }
}
