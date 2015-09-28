<?php

namespace Scheduler\Web\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;

class GetEmployeeHoursWeeklyInput extends Input
{
    public function __invoke(Request $request)
    {
        $currentUser = $this->getCurrentUser($request);
        $employeeId = (int) $request->getAttribute('id');

        $params = $request->getQueryParams();
        $weekOf = isset($params["date"]) ? $params["date"] : '';

        return [$currentUser, $employeeId, $weekOf];
    }
}
