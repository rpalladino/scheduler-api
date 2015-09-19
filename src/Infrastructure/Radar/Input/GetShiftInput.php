<?php

namespace Scheduler\Infrastructure\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;

class GetShiftInput extends Input
{
    public function __invoke(Request $request)
    {
        $currentUser = $this->getCurrentUser($request);
        $shiftId = (int) $request->getAttribute('id');

        $params = $request->getQueryParams();
        $withCoworkers = isset($params["with_coworkers"])
            ? (boolean) $params["with_coworkers"]
            : false;

        return [$currentUser, $shiftId, $withCoworkers];
    }
}
