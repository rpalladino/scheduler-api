<?php

namespace spec\Scheduler\Infrastructure\Radar\Responder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\REST\Resource\ShiftResource;
use Scheduler\REST\Resource\UserResource;

class ShiftResponderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new ShiftResource(new UserResource()));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('\Radar\Adr\Responder\Responder');
    }
}
