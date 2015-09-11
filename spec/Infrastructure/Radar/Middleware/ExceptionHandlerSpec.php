<?php

namespace spec\Scheduler\Infrastructure\Radar\Middleware;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Zend\Diactoros\ServerRequestFactory as Request;
use Zend\Diactoros\Response;

class ExceptionHandlerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Response());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Relay\Middleware\ExceptionHandler');
    }

    function it_gives_exception_message_in_dev()
    {
        putenv("APP_ENV=dev");

        $newResponse = $this(Request::fromGlobals(), new Response(), function ($request, $response) {
            throw new \Exception("Exception message");
        });

        $newResponse->getBody()->__toString()->shouldBe("Exception message");
    }

    function it_gives_generic_message_in_prod()
    {
        putenv("APP_ENV=prod");

        $newResponse = $this(Request::fromGlobals(), new Response(), function ($request, $response) {
            throw new \Exception("Exception message");
        });

        $self = $this->getWrappedObject();
        $newResponse->getBody()->__toString()->shouldBe($self::SERVER_ERROR_MESSAGE);
    }
}
