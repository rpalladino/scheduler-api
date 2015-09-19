<?php

namespace spec\Scheduler\Infrastructure\Radar\Responder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\REST\Resource\ShiftResource;
use Scheduler\REST\Resource\UserResource;

class ShiftItemResponderSpec extends ObjectBehavior
{
    function let()
    {
        $userResource = new UserResource();
        $this->beConstructedWith(new ShiftResource($userResource), $userResource);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Infrastructure\Radar\Responder\ShiftItemResponder');
    }
}
