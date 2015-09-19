<?php

namespace spec\Scheduler\Infrastructure\Radar\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
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
                            "manager_id" => 1,
                            "employee_id" => 3,
                            "start_time" => (new DateTime("07:30 AM"))->format(DATE_RFC2822),
                            "end_time" => (new DateTime("11:30 AM"))->format(DATE_RFC2822),
                            "break" => 0.5
                        ]);

        $input = $this($request);

        $input->shouldBeArray();
        $input->shouldHaveCount(6);
    }
}
