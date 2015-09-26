<?php

namespace Scheduler\Infrastructure\Radar\Responder;

use Crell\ApiProblem\ApiProblem;

class ApiProblemResponder extends \Radar\Adr\Responder\Responder
{
    protected function notFound()
    {
        $this->response = $this->response->withStatus(404);

        $problem = $this->createProblem();
        $problem->setDetail($this->payload->getMessages());

        $this->problemBody($problem);
    }

    protected function notValid()
    {
        $this->response = $this->response->withStatus(422);

        $problem = $this->createProblem();
        $problem->setDetail("The parameters specified are not valid.");

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

    protected function notAuthenticated()
    {
        $this->response = $this->response->withStatus(401)
                                         ->withHeader("WWW-Authenticate", "None");

        $problem = $this->createProblem();
        $problem->setDetail("This resource requires a valid access token.");

        $this->problemBody($problem);
    }

    protected function notAuthorized()
    {
        $this->response = $this->response->withStatus(403);

        $problem = $this->createProblem();
        $problem->setDetail("You are not authorized to access this resource.");

        $this->problemBody($problem);
    }

    protected function createProblem()
    {
        $problem = new ApiProblem($this->response->getReasonPhrase());
        $problem->setStatus($this->response->getStatusCode());
        $problem->setInstance((string) $this->request->getUri());

        return $problem;
    }

    protected function problemBody(ApiProblem $problem)
    {
        $this->response = $this->response->withHeader('Content-Type', 'application/problem+json');
        $this->response->getBody()->write($problem->asJson());
    }
}
