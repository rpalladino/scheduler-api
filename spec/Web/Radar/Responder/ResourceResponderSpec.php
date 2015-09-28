<?php

namespace spec\Scheduler\Web\Radar\Responder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResourceResponderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Web\Radar\Responder\ResourceResponder');
    }
}
