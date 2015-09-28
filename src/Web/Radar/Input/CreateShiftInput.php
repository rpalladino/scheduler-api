<?php

namespace Scheduler\Web\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;

class CreateShiftInput extends Input
{
    public function __invoke(Request $request)
    {
        $user = $this->getCurrentUser($request);

        $body = $request->getParsedBody();
        $start = isset($body->start) ? $body->start : '';
        $end = isset($body->end) ? $body->end : '';
        $break = isset($body->break) ? $body->break : '';

        return [$user, $start, $end, $break];
    }
}
