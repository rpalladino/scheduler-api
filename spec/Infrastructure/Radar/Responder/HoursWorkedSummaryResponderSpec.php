<?php

namespace spec\Scheduler\Infrastructure\Radar\Responder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\REST\Resource\HoursWorkedSummaryResource;

class HoursWorkedSummaryResponderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new HoursWorkedSummaryResource());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Infrastructure\Radar\Responder\HoursWorkedSummaryResponder');
    }
}
