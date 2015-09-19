<?php

namespace spec\Scheduler\Infrastructure\Radar\Responder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResourceResponderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Infrastructure\Radar\Responder\ResourceResponder');
    }
}
