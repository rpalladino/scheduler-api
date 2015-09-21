<?php

namespace spec\Scheduler\Infrastructure\Radar\Input;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\Domain\Model\User\Authenticator;
use Zend\Diactoros\ServerRequest as Request;

class InputSpec extends ObjectBehavior
{
    function let(Authenticator $authenticator)
    {
        $this->beConstructedWith($authenticator);
    }

    function it_is_invokable(Request $request)
    {
        $this($request)->shouldBeArray();
    }
}
