<?php

namespace spec\Scheduler\Application\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
use Aura\Payload\Payload;
use Scheduler\Application\Service\CreateShift;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class CreateShiftSpec extends ObjectBehavior
{
    function let(ShiftMapper $shiftMapper)
    {
        $this->beConstructedWith($shiftMapper);
    }

    function it_is_invokable()
    {
        expect(method_exists($this->getWrappedObject(), '__invoke'))->toBe(true);
    }

    function it_creates_a_new_shift($shiftMapper)
    {
        $manager = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $manager->authenticate();

        $payload = $this($manager, "1:30 PM", "7:30 PM", 0.75);

        $shiftMapper->insert(Argument::type(Shift::class))->shouldHaveBeenCalled();
        $payload->shouldImplement(Payload::class);
        $payload->getStatus()->shouldBe(Payload::CREATED);
        $payload->getOutput()->shouldHaveType(Shift::class);
        $payload->getOutput()->getManager()->shouldBe($manager);
        $payload->getOutput()->getEmployee()->shouldHaveType(User::class);
        $payload->getOutput()->getStartTime()->shouldBeLike(new DateTime("1:30 PM"));
        $payload->getOutput()->getEndTime()->shouldBeLike(new DateTime("7:30 PM"));
        $payload->getOutput()->getBreak()->shouldBe(0.75);
    }

    function it_only_allows_authenticated_managers_to_create_shifts()
    {
        $manager = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");

        $payload = $this($manager, "1:30 PM", "7:30 PM", 0.75);

        $payload->getStatus()->shouldBe(Payload::NOT_AUTHENTICATED);
    }

    function it_does_not_allow_employees_to_create_shifts()
    {
        $employee = new User(2, "Richard Roma", "employee", "ricky@roma.com");
        $employee->authenticate();

        $payload = $this($employee, "1:30 PM", "7:30 PM", 0.75);

        $payload->getStatus()->shouldBe(Payload::NOT_AUTHORIZED);
    }

    function it_returns_not_valid_when_start_date_is_invalid()
    {
        $manager = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $manager->authenticate();

        $payload = $this($manager, 'not-a-datetime', '7:30 PM', 0.75);

        $payload->getStatus()->shouldBe(Payload::NOT_VALID);
        $payload->getMessages()->shouldHaveKeyWithValue("start", CreateShift::INVALID_DATE_MESSAGE);
    }

    function it_returns_not_valid_when_end_date_is_invalid()
    {
        $manager = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $manager->authenticate();

        $payload = $this($manager, '1:30 PM', 'not-a-datetime', 0.75);

        $payload->getStatus()->shouldBe(Payload::NOT_VALID);
        $payload->getMessages()->shouldHaveKeyWithValue("end", CreateShift::INVALID_DATE_MESSAGE);
    }

    function it_defaults_to_zero_when_break_is_invalid()
    {
        $manager = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $manager->authenticate();

        $payload = $this($manager, '1:30 PM', '7:30', "NaN");

        $payload->getOutput()->getBreak()->shouldBe(0.0);
    }

}
