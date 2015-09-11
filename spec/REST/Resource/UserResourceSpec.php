<?php

namespace spec\Scheduler\REST\Resource;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\Domain\Model\User\User;

class UserResourceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\REST\Resource\UserResource');
    }

    function it_can_transform_a_single_user()
    {
        $user = new User(65432, "Richard Roma", "employee", "ricky@roma.com", "312-322-2211");

        $result = $this->transform($user);

        $result->shouldBeArray();
        $result->shouldHaveKeyWithValue("id", 65432);
        $result->shouldHaveKeyWithValue("name", "Richard Roma");
        $result->shouldHaveKeyWithValue("email", "ricky@roma.com");
        $result->shouldHaveKeyWithValue("phone", "312-322-2211");
    }
}
