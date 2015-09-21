<?php

namespace spec\Scheduler\Infrastructure\Radar\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Zend\Diactoros\ServerRequest as Request;
use Scheduler\Infrastructure\Auth\TokenAuthenticator;

class GetEmployeeHoursWeeklyInputSpec extends ObjectBehavior
{
    function let(TokenAuthenticator $authenticator)
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
                        ->withHeader("x-access-token", "i_am_an_employee")
                        ->withAttribute("id", 1)
                        ->withQueryParams([
                            "date" => (new \DateTime())->format(DATE_RFC3339)
                        ]);

        $input = $this($request);

        $input->shouldBeArray();
        $input->shouldHaveCount(3);
    }
}
