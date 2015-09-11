<?php

namespace Scheduler\Infrastructure\Radar\Middleware;

use Exception;
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
            $response = $this->exceptionResponse->withStatus(500);

            if (getenv("APP_ENV") == "dev") {
                $response->getBody()->write($e->getMessage());
            } else {
                $response->getBody()->write(self::SERVER_ERROR_MESSAGE);
            }
        }

        return $response;
    }
}
