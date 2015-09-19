<?php

namespace spec\Scheduler\Infrastructure\Radar\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Zend\Diactoros\ServerRequest as Request;
use Scheduler\Infrastructure\Auth\TokenAuthenticator;

class UpdateShiftInputSpec extends ObjectBehavior
{
    function let(TokenAuthenticator $authenticator)
    {
        $this->beConstructedWith($authenticator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Infrastructure\Radar\Input\Input');
    }

    function it_is_invokable(Request $request)
    {
        $this($request)->shouldBeArray();
    }

    function it_extracts_input_from_request()
    {
        $request = (new Request())
                        ->withHeader("x-access-token", "i_am_a_manager")
                        ->withAttribute("id", 1)
                        ->withParsedBody([
                            "employee_id" => 3
                        ]);

        $input = $this($request);

        $input->shouldBeArray();
        $input->shouldHaveCount(3);
    }
}
