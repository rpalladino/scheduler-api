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

    function it_responds_with_message_in_api_problem_format()
    {
        $newResponse = $this(Request::fromGlobals(), new Response(), $this->exception());

        $parsedBody = (array) json_decode((string) $newResponse->getBody()->getWrappedObject());
        // eval(\Psy\sh());
        expect($parsedBody)->toHaveKey('title');
        expect($parsedBody)->toHaveKey('status');
        expect($parsedBody)->toHaveKey('detail');
        expect($parsedBody)->toHaveKey('instance');
    }

    function it_gives_exception_message_in_dev()
    {
        putenv("APP_ENV=dev");

        $newResponse = $this(Request::fromGlobals(), new Response(), $this->exception());

        $newResponse->getBody()->__toString()->shouldMatch("/Exception message/i");
    }

    function it_gives_generic_message_in_prod()
    {
        putenv("APP_ENV=prod");

        $newResponse = $this(Request::fromGlobals(), new Response(), $this->exception());

        $self = $this->getWrappedObject();
        $newResponse->getBody()->__toString()->shouldMatch("/" . $self::SERVER_ERROR_MESSAGE . "/i");
    }

    private function exception()
    {
        return function ($request, $response) {
            throw new \Exception("Exception message");
        };
    }
}
