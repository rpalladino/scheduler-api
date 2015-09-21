<?php

namespace Scheduler\Infrastructure\Radar\Responder;

use Scheduler\REST\Resource\HoursWorkedSummaryResource;

class HoursWorkedSummaryResponder extends ResourceResponder
{
    public function __construct(HoursWorkedSummaryResource $resource)
    {
        $this->resource = $resource;
    }
}
