<?php

namespace Scheduler\Web\Radar\Responder;

class ResourceResponder extends ApiProblemResponder
{
    protected $resource;

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
