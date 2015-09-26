<?php

namespace spec\Scheduler\Domain\Model\User;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Model\User\UserMapper;

class InMemoryAuthenticatorSpec extends ObjectBehavior
{

    function let(UserMapper $userMapper)
    {
        $this->beConstructedWith($userMapper, []);
    }

    function it_gets_user_for_access_token($userMapper)
    {
        $token = "i_am_an_employee";
        $employee = new User(1, "Richard Roma", "employee", "ricky@roma.com");
        $userMapper->find(1)->willReturn($employee);
        $tokenMap = [
            "i_am_an_employee" => 1
        ];
        $this->beConstructedWith($userMapper, $tokenMap);

        $user = $this->getUserForToken($token);
        $user->shouldBe($employee);
        $user->shouldBeAuthenticated();
    }

    function it_returns_null_user_if_token_not_valid()
    {
        $token = "invalid_token";
        $user = $this->getUserForToken($token);
        $user->shouldBeLike(new NullUser());
        $user->shouldNotBeAuthenticated();
    }
}
