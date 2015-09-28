<?php

namespace spec\Scheduler\Web\Radar\Middleware;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

class ApiProblemExceptionHandlerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Response());
    }

    function it_responds_with_message_in_api_problem_format()
    {
        $response = $this(new Request(), new Response(), $this->exception());
        $response->getBody()->__toString()->shouldMatch("/status/i");
        $response->getBody()->__toString()->shouldMatch("/title/i");
        $response->getBody()->__toString()->shouldMatch("/type/i");
        $response->getBody()->__toString()->shouldMatch("/detail/i");
    }

    function it_gives_exception_message_in_dev()
    {
        putenv("APP_ENV=dev");

        $response = $this(new Request(), new Response(), $this->exception());

        $response->getBody()->__toString()->shouldMatch("/Exception message/i");
    }

    function it_gives_generic_message_in_prod()
    {
        putenv("APP_ENV=prod");

        $response = $this(new Request(), new Response(), $this->exception());

        $self = $this->getWrappedObject();
        $response->getBody()->__toString()->shouldMatch("/" . $self::SERVER_ERROR_MESSAGE . "/i");
    }

    private function exception()
    {
        return function ($request, $response) {
            throw new \Exception("Exception message");
        };
    }
}
