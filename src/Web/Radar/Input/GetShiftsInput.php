<?php

namespace Scheduler\Web\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;

class GetShiftsInput extends Input
{
    public function __invoke(Request $request)
    {
        $user = $this->getCurrentUser($request);

        $params = $request->getQueryParams();
        $start = isset($params["start"]) ? $params["start"] : '';
        $end = isset($params["end"]) ? $params["end"] : '';

        return [$user, $start, $end];
    }
}
