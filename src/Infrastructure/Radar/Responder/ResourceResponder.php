<?php

namespace Scheduler\Infrastructure\Radar\Responder;

class ResourceResponder extends \Radar\Adr\Responder\Responder
{
    use ApiProblemResponder;

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
