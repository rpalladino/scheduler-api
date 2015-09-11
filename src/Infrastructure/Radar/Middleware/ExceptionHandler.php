<?php

namespace Scheduler\Infrastructure\Radar\Middleware;

use Exception;
use Crell\ApiProblem\ApiProblem;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ExceptionHandler extends \Relay\Middleware\ExceptionHandler
{
    const SERVER_ERROR_MESSAGE = "We're experiencing some trouble.";

    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            $response = $next($request, $response);
        } catch (Exception $e) {
            $response = $this->exceptionResponse
                                ->withStatus(500)
                                ->withHeader('Content-Type', 'application/problem+json');

            $problem = new ApiProblem();
            $problem->setTitle($response->getReasonPhrase());
            $problem->setStatus(500);
            $problem->setInstance((string) $request->getUri());

            if (getenv("APP_ENV") == "dev") {
                $problem->setDetail($e->getMessage());
            } else {
                $problem->setDetail(self::SERVER_ERROR_MESSAGE);
            }

            $response->getBody()->write($problem->asJson());
        }

        return $response;
    }
}
