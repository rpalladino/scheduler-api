<?php

namespace spec\Scheduler\Application\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
use Aura\Payload\Payload;
use Scheduler\Application\Service;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class GetShiftsAssignedToEmployeeSpec extends ObjectBehavior
{
    function let(ShiftMapper $shiftMapper)
    {
        $this->beConstructedWith($shiftMapper);
    }

    function it_is_invokable()
    {
        expect(method_exists($this->getWrappedObject(), '__invoke'))->toBe(true);
    }

    function it_gets_shifts_assigned_to_employee($shiftMapper)
    {
        $manager = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $employee = new User(2, "Richard Roma", "employee", "richard@roma.com");
        $employee->authenticate();

        $shifts = [
            new Shift(1, $manager, $employee, 0.0, new DateTime('2015-09-06 08:30 AM'), new DateTime('2015-09-06 01:30 PM')),
            new Shift(2, $manager, $employee, 0.0, new DateTime('2015-09-06 01:30 PM'), new DateTime('2015-09-06 07:30 PM'))
        ];

        $start = new DateTime('2015-09-06');
        $end = new DateTime('2015-09-07');
        $shiftMapper->findShiftsByEmployeeId($employee->getId())->willReturn($shifts);

        $payload = $this($employee, 2);

        $payload->shouldImplement(Payload::class);
        $payload->getOutput()->shouldHaveCount(2);
        $payload->getOutput()->shouldContain($shifts[0]);
        $payload->getOutput()->shouldContain($shifts[1]);
    }

    function it_does_not_allow_current_user_to_get_shifts_assigned_to_another_employee()
    {
        $currentUser = new User(2, "Richard Roma", "employee", "richard@roma.com");
        $currentUser->authenticate();

        $payload = $this($currentUser, 3);

        $payload->shouldImplement(Payload::class);
        $payload->getStatus()->shouldBe(Payload::NOT_AUTHORIZED);
    }

    function it_returns_not_authenticated_status_when_user_is_not_authenticated()
    {
        $employee = new User(2, "Richard Roma", "employee", "richard@roma.com");

        $payload = $this($employee, 2);

        $payload->shouldImplement(Payload::class);
        $payload->getStatus()->shouldBe(Payload::NOT_AUTHENTICATED);
    }

    function it_returns_not_valid_when_employee_id_is_invalid()
    {
        $employee = new User(2, "Richard Roma", "employee", "richard@roma.com");
        $employee->authenticate();

        $payload = $this($employee, "asf3va");

        $payload->shouldImplement(Payload::class);
        $payload->getStatus()->shouldBe(Payload::NOT_VALID);
        $payload->getMessages()->shouldHaveKeyWithValue("employee_id", Service\GetShiftsAssignedToEmployee::INVALID_ID_MESSAGE);
    }
}
