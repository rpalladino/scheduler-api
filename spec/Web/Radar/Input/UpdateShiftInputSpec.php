<?php

namespace spec\Scheduler\Web\Radar\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
use Scheduler\Domain\Model\User\Authenticator;
use Zend\Diactoros\ServerRequest as Request;

class UpdateShiftInputSpec extends ObjectBehavior
{
    function let(Authenticator $authenticator)
    {
        $this->beConstructedWith($authenticator);
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
