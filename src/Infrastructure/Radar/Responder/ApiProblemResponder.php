<?php

namespace Scheduler\Infrastructure\Radar\Responder;

use Crell\ApiProblem\ApiProblem;

trait ApiProblemResponder
{
    protected function notValid()
    {
        $this->response = $this->response->withStatus(422);

        $problem = new ApiProblem($this->response->getReasonPhrase());
        $problem->setDetail("The parameters specified are not valid.");
        $problem->setInstance((string) $this->request->getUri());

        if (is_array($this->payload->getMessages())) {
            $problem["invalid-params"] = [];
            foreach ($this->payload->getMessages() as $name => $reason) {
                $problem["invalid-params"][] = [
                    "name" => $name,
                    "reason" => $reason
                ];
            }
        }

        $this->problemBody($problem);
    }

    protected function problemBody(ApiProblem $problem)
    {
        $this->response = $this->response->withHeader('Content-Type', 'application/problem+json');
        $this->response->getBody()->write($problem->asJson());
    }
}
