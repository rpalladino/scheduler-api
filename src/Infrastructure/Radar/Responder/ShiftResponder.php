<?php

namespace Scheduler\Infrastructure\Radar\Responder;

use Scheduler\REST\Resource\ShiftResource;

class ShiftResponder extends \Radar\Adr\Responder\Responder
{
    use ApiProblemResponder;

    private $resource;

    public function __construct(ShiftResource $resource)
    {
        $this->resource = $resource;
    }

    protected function success()
    {
        $this->response = $this->response->withStatus(200);
        $output = $this->payload->getOutput();

        if (is_array($output)) {
            $this->jsonBody($this->resource->collection($output));
        } else {
            $this->jsonBody($this->resource->item($output));
        }
    }
}
