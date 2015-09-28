<?php

namespace Scheduler\Web\Radar\Responder;

use Scheduler\REST\Resource\ShiftResource;

class ShiftResponder extends ResourceResponder
{
    public function __construct(ShiftResource $resource)
    {
        $this->resource = $resource;
    }

    protected function created()
    {
        $shift = $this->payload->getOutput();
        $url = "/shifts/{$shift->getId()}";

        $this->response = $this->response->withStatus(201)
                                         ->withHeader("Location", $url);
        $this->jsonBody($this->resource->item($shift));
    }

    protected function updated()
    {
        $this->response = $this->response->withStatus(200);
        $output = $this->payload->getOutput();
        $this->jsonBody($this->resource->item($output));
    }
}
