<?php

namespace spec\Scheduler\Web\Radar\Responder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\REST\Resource\UserResource;

class UserResponderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new UserResource());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Radar\Adr\Responder\Responder');
    }
}
