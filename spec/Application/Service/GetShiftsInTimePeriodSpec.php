<?php

namespace spec\Scheduler\Application\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
use Aura\Payload\Payload;
use Scheduler\Application\Service\GetShiftsInTimePeriod;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class GetShiftsInTimePeriodSpec extends ObjectBehavior
{
    function let(ShiftMapper $shiftMapper)
    {
        $this->beConstructedWith($shiftMapper);
    }

    function it_is_invokable()
    {
        expect(method_exists($this->getWrappedObject(), '__invoke'))->toBe(true);
    }

    function it_gets_shifts_in_time_period(ShiftMapper $shiftMapper)
    {
        $manager = User::managerNamedWithEmail("John Williamson", "jwilliamson@gmail.com");
        $manager->authenticate();

        $shifts = [
            Shift::withManagerAndTimes(
                $manager,
                new DateTime('2015-09-06 08:30 AM'),
                new DateTime('2015-09-06 01:30 PM')
            ),
            Shift::withManagerAndTimes(
                $manager,
                new DateTime('2015-09-06 01:00 PM'),
                new DateTime('2015-09-06 07:00 PM')
            )
        ];

        $start = new DateTime('2015-09-06');
        $end = new DateTime('2015-09-07');
        $shiftMapper->findShiftsInTimePeriod($start, $end)->willReturn($shifts);

        $payload = $this($manager, '2015-09-06', '2015-09-07');

        $payload->shouldImplement(Payload::class);
        $payload->getOutput()->shouldHaveCount(2);
        $payload->getOutput()->shouldContain($shifts[0]);
        $payload->getOutput()->shouldContain($shifts[1]);
    }

    function it_returns_not_authenticated_status_when_user_is_not_authenticated()
    {
        $manager = User::managerNamedWithEmail("John Williamson", "jwilliamson@gmail.com");

        $payload = $this($manager, '2015-09-06', '2015-09-07');

        $payload->shouldImplement(Payload::class);
        $payload->getStatus()->shouldBe(Payload::NOT_AUTHENTICATED);
    }

    function it_returns_not_authorized_when_user_is_not_a_manager(User $user)
    {
        $employee = User::employeeNamedWithEmail("Richard Roma", "ricky@roma.com");
        $employee->authenticate();

        $payload = $this($employee, '2015-09-06', '2015-09-07');

        $payload->shouldImplement(Payload::class);
        $payload->getStatus()->shouldBe(Payload::NOT_AUTHORIZED);
    }

    function it_returns_not_valid_when_start_date_is_invalid()
    {
        $manager = User::managerNamedWithEmail("John Williamson", "jwilliamson@gmail.com");
        $manager->authenticate();

        $payload = $this($manager, 'not-a-date', '2015-09-07');

        $payload->shouldImplement(Payload::class);
        $payload->getStatus()->shouldBe(Payload::NOT_VALID);
        $payload->getMessages()->shouldHaveKeyWithValue("start", GetShiftsInTimePeriod::INVALID_DATE_MESSAGE);
    }

    function it_returns_not_valid_when_end_date_is_invalid()
    {
        $manager = User::managerNamedWithEmail("John Williamson", "jwilliamson@gmail.com");
        $manager->authenticate();

        $payload = $this($manager, '2015-09-07', 'not-a-date');

        $payload->shouldImplement(Payload::class);
        $payload->getStatus()->shouldBe(Payload::NOT_VALID);
        $payload->getMessages()->shouldHaveKeyWithValue("end", GetShiftsInTimePeriod::INVALID_DATE_MESSAGE);
    }
}
