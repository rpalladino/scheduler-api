<?php

namespace spec\Scheduler\Infrastructure\Radar\Responder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('\Radar\Adr\Responder\Responder');
    }
}
