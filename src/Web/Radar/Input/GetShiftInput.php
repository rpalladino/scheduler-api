<?php

namespace Scheduler\Web\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;

class GetShiftInput extends Input
{
    public function __invoke(Request $request)
    {
        $currentUser = $this->getCurrentUser($request);
        $shiftId = (int) $request->getAttribute('id');

        $params = $request->getQueryParams();
        $withCoworkers = isset($params["with_coworkers"])
            ? $params["with_coworkers"] == "true"
            : false;

        return [$currentUser, $shiftId, $withCoworkers];
    }
}
