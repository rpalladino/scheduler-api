<?php

namespace spec\Scheduler\Infrastructure\Radar\Responder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Aura\Payload\Payload;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

class ApiProblemResponderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('Scheduler\Infrastructure\Radar\Responder\Responder');
    }

    function it_responds_with_problem_when_parameters_not_valid()
    {
        $payload = new Payload();
        $payload->setStatus($payload::NOT_VALID);
        $payload->setMessages(["invalid-params" => ["foo" => "bar"]]);

        $response = $this(new Request(), new Response(), $payload);

        $response->getStatusCode()->shouldBe(422);
        $response->getBody()->__toString()->shouldMatch("/status/i");
        $response->getBody()->__toString()->shouldMatch("/title/i");
        $response->getBody()->__toString()->shouldMatch("/type/i");
        $response->getBody()->__toString()->shouldMatch("/detail/i");
        $response->getBody()->__toString()->shouldMatch("/invalid-params/i");
    }

    function it_responds_with_problem_when_user_not_authenticated()
    {
        $payload = new Payload();
        $payload->setStatus($payload::NOT_AUTHENTICATED);

        $response = $this(new Request(), new Response(), $payload);

        $response->getStatusCode()->shouldBe(401);
        $response->getBody()->__toString()->shouldMatch("/status/i");
        $response->getBody()->__toString()->shouldMatch("/title/i");
        $response->getBody()->__toString()->shouldMatch("/type/i");
        $response->getBody()->__toString()->shouldMatch("/detail/i");
    }

    function it_responds_with_problem_when_user_not_authorizedd()
    {
        $payload = new Payload();
        $payload->setStatus($payload::NOT_AUTHORIZED);

        $response = $this(new Request(), new Response(), $payload);

        $response->getStatusCode()->shouldBe(403);
        $response->getBody()->__toString()->shouldMatch("/status/i");
        $response->getBody()->__toString()->shouldMatch("/title/i");
        $response->getBody()->__toString()->shouldMatch("/type/i");
        $response->getBody()->__toString()->shouldMatch("/detail/i");
    }
}
