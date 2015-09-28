<?php

namespace spec\Scheduler\Web\Radar\Responder;

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
        $this->shouldHaveType('Scheduler\Web\Radar\Responder\HoursWorkedSummaryResponder');
    }
}
