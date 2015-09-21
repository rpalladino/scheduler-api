<?php

namespace spec\Scheduler\Application\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Aura\Payload\Payload;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Model\User\UserMapper;

class GetEmployeeSpec extends ObjectBehavior
{
    function let(UserMapper $userMapper)
    {
        $this->beConstructedWith($userMapper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Application\Service\GetEmployee');
    }

    function it_gets_an_employee($userMapper)
    {
        $currentUser = new User(1, "John Williamson", "manager", "john@abc.com");
        $currentUser->authenticate();
        $employee = new User(3, "Shelly Levene", "employee", "oldguy@aol.com");
        $userMapper->find(3)->willReturn($employee);

        $payload = $this($currentUser, 3);

        $payload->shouldHaveType(Payload::class);
        $payload->getStatus()->shouldBe(Payload::SUCCESS);
        $payload->getOutput()->shouldBe($employee);
    }

    function it_does_not_allow_unauthenticated_access()
    {
        $currentUser = new User(1, "John Williamson", "manager", "john@abc.com");

        $payload = $this($currentUser, 3);

        $payload->getStatus()->shouldBe(Payload::NOT_AUTHENTICATED);
    }

    function it_returns_not_found_when_employee_not_found()
    {
        $currentUser = new User(1, "John Williamson", "manager", "john@abc.com");
        $currentUser->authenticate();

        $payload = $this($currentUser, 43564356);

        $payload->getStatus()->shouldBe(Payload::NOT_FOUND);
    }
}
