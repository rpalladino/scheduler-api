<?php

namespace spec\Scheduler\Infrastructure\Radar\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\Infrastructure\Auth\TokenAuthenticator;

class InputSpec extends ObjectBehavior
{
    function let(TokenAuthenticator $authenticator)
    {
        $this->beConstructedWith($authenticator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Infrastructure\Radar\Input\Input');
    }
}
