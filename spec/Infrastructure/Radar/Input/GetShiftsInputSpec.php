<?php

namespace spec\Scheduler\Infrastructure\Radar\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\Domain\Model\User\Authenticator;
use Zend\Diactoros\ServerRequest as Request;

class GetShiftsInputSpec extends ObjectBehavior
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
                        ->withQueryParams([
                            "start" =>"2015-09-01T13:30:00",
                            "end" => "2015-09-01T19:30:00"
                        ]);

        $input = $this($request);

        $input->shouldBeArray();
        $input->shouldHaveCount(3);
    }
}
