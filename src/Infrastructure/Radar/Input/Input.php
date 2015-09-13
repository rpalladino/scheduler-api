<?php

namespace Scheduler\Infrastructure\Radar\Input;

use Psr\Http\Message\ServerRequestInterface as Request;
use Scheduler\Infrastructure\Auth\TokenAuthenticator;

class Input
{
    private $authenticator;

    public function __construct(TokenAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function __invoke(Request $request)
    {
        return [$this->getCurrentUser($request)];
    }

    protected function getCurrentUser(Request $request)
    {
        $token = $request->getHeaderLine("x-access-token");

        return $this->authenticator->getUserForToken($token);
    }
}
