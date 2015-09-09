<?php

namespace spec\Scheduler\Domain\Model\User;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NullUserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Domain\Model\User\User');
    }

    function it_retrieves_all_properties_as_null()
    {
        $this->getId()->shouldReturn(null);
        $this->getName()->shouldReturn(null);
        $this->getEmail()->shouldReturn(null);
        $this->getPhone()->shouldReturn(null);
        $this->getCreated()->shouldReturn(null);
        $this->getUpdated()->shouldReturn(null);
    }
}
