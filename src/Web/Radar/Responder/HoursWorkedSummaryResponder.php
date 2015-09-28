<?php

namespace Scheduler\Web\Radar\Responder;

use Scheduler\Web\Resource\HoursWorkedSummaryResource;

class HoursWorkedSummaryResponder extends ResourceResponder
{
    public function __construct(HoursWorkedSummaryResource $resource)
    {
        $this->resource = $resource;
    }
}
