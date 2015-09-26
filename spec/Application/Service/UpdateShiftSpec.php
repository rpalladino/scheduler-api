<?php

namespace spec\Scheduler\Application\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
use Aura\Payload\Payload;
use Scheduler\Application\Service\UpdateShift;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Model\User\UserMapper;

class UpdateShiftSpec extends ObjectBehavior
{
    function let(ShiftMapper $shiftMapper, UserMapper $userMapper)
    {
        $this->beConstructedWith($shiftMapper, $userMapper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Application\Service\UpdateShift');
    }

    function it_updates_a_shift($shiftMapper, $userMapper)
    {
        $currentUser = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $currentUser->authenticate();

        $shiftId = 1;
        $employeeId = 3;

        $start = new DateTime("5:30 AM");
        $end = new DateTime("9:30 AM");
        $shift = new Shift($shiftId, $currentUser, new NullUser, 0.5, $start, $end);
        $shiftMapper->find($shiftId)->willReturn($shift);
        $shiftMapper->update(Argument::type(Shift::class))->shouldBeCalled();

        $employee = new User($employeeId, "Shelly Levene", "employee", "oldguy@aol.com");
        $userMapper->find($employeeId)->willReturn($employee);

        $payload = $this($currentUser, $shiftId, $employeeId, "07:30 AM", "11:30 AM", 0.75);

        $payload->getStatus()->shouldBe(Payload::UPDATED);
        $payload->getOutput()->shouldHaveType(Shift::class);
        $payload->getOutput()->getEmployee()->getId()->shouldBe($employeeId);
        $payload->getOutput()->getStartTime()->shouldBeLike(new DateTime("07:30 AM"));
        $payload->getOutput()->getEndTime()->shouldBeLike(new DateTime("11:30 AM"));
        $payload->getOutput()->getBreak()->shouldBe(0.75);
    }

    function it_does_not_assign_employee_if_employee_id_is_null($shiftMapper, $userMapper)
    {
        $currentUser = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $currentUser->authenticate();

        $shiftId = 1;
        $employeeId = null;

        $employee = new NullUser();
        $userMapper->find($employeeId)->willReturn($employee);

        $start = new DateTime("5:30 AM");
        $end = new DateTime("9:30 AM");
        $shift = new Shift($shiftId, $currentUser, $employee, 0.5, $start, $end);
        $shiftMapper->find($shiftId)->willReturn($shift);
        $shiftMapper->update(Argument::type(Shift::class))->shouldBeCalled();

        $payload = $this($currentUser, $shiftId, $employeeId, "07:30 AM", "11:30 AM", 0.75);
        $payload->getStatus()->shouldBe(Payload::UPDATED);
    }

    function it_does_not_allow_unauthenticated_users_to_update_shifts()
    {
        $currentUser = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");

        $payload = $this($currentUser, 1, 3, "07:30 AM", "11:30 AM", 0.5);

        $payload->getStatus()->shouldBe(Payload::NOT_AUTHENTICATED);
    }

    function it_does_not_allow_employees_to_update_shifts()
    {
        $currentUser = new User(2, "Richard Roma", "employee", "ricky@roma.com");
        $currentUser->authenticate();

        $payload = $this($currentUser, 1, 2, "07:30 AM", "11:30 AM", 0.5);

        $payload->getStatus()->shouldBe(Payload::NOT_AUTHORIZED);
    }

    function it_returns_not_found_message_when_shift_not_found($shiftMapper)
    {
        $currentUser = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $currentUser->authenticate();

        $shiftMapper->find(1)->willReturn(null);

        $payload = $this($currentUser, 1, 2, "07:30 AM", "11:30 AM", 0.5);

        $payload->getStatus()->shouldBe(Payload::NOT_FOUND);
        $payload->getMessages()->shouldBe(UpdateShift::SHIFT_NOT_FOUND_MESSAGE);
    }

    function it_returns_not_found_message_when_user_not_found($shiftMapper, $userMapper)
    {
        $currentUser = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $currentUser->authenticate();

        $start = new DateTime("5:30 AM");
        $end = new DateTime("9:30 AM");
        $shift = new Shift(1, $currentUser, new NullUser, 0.5, $start, $end);
        $shiftMapper->find(1)->willReturn($shift);

        $userMapper->find(2)->willReturn(new NullUser());

        $payload = $this($currentUser, 1, 2, "07:30 AM", "11:30 AM", 0.5);

        $payload->getStatus()->shouldBe(Payload::NOT_FOUND);
        $payload->getMessages()->shouldBe(UpdateShift::USER_NOT_FOUND_MESSAGE);
    }

    function it_returns_not_valid_when_start_date_is_invalid()
    {
        $currentUser = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $currentUser->authenticate();

        $payload = $this($currentUser, 1, 3, "not a datetime", "11:30 AM", 0.5);

        $payload->getStatus()->shouldBe(Payload::NOT_VALID);
        $payload->getMessages()->shouldHaveKeyWithValue("start", UpdateShift::INVALID_DATE_MESSAGE);
    }

    function it_returns_not_valid_when_end_date_is_invalid()
    {
        $currentUser = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $currentUser->authenticate();

        $payload = $this($currentUser, 1, 3, "07:30 AM", "not a datetime", 0.5);

        $payload->getStatus()->shouldBe(Payload::NOT_VALID);
        $payload->getMessages()->shouldHaveKeyWithValue("end", UpdateShift::INVALID_DATE_MESSAGE);
    }
}
