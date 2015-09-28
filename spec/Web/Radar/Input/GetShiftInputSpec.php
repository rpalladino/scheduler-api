<?php

namespace spec\Scheduler\Web\Radar\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\Domain\Model\User\Authenticator;
use Zend\Diactoros\ServerRequest as Request;

class GetShiftInputSpec extends ObjectBehavior
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
                        ->withQueryParams([
                            "with_coworkers" => "true"
                        ]);

        $input = $this($request);

        $input->shouldBeArray();
        $input->shouldHaveCount(3);
    }
}
