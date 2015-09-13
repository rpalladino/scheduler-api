<?php

namespace Scheduler\Infrastructure\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;

class GetShiftsInput extends Input
{
    public function __invoke(Request $request)
    {
        $user = $this->getCurrentUser($request);
        $start = $request->getQueryParams()["start"];
        $end = $request->getQueryParams()["end"];

        return [$user, $start, $end];
    }
}
