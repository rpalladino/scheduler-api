<?php

namespace spec\Scheduler\Application\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTimeImmutable;
use Aura\Payload\Payload;
use Scheduler\Application\Service\GetHoursWorkedInWeek;
use Scheduler\Domain\Model\Shift\HoursWorkedSummary;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Service\HoursWorkedCalculator;

class GetHoursWorkedInWeekSpec extends ObjectBehavior
{
    function let(ShiftMapper $shiftMapper)
    {
        $calculator = new HoursWorkedCalculator($shiftMapper->getWrappedObject());
        $this->beConstructedWith($calculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Application\Service\GetHoursWorkedInWeek');
    }

    function it_gets_hours_worked_in_week_summary($shiftMapper)
    {
        $currentUser = new User(1234, "Richard Roma", "employee", "ricky@roma.com");
        $currentUser->authenticate();

        $weekStart = new DateTimeImmutable("2015-10-05");
        $weekEnd = new DateTimeImmutable("2015-10-11");
        $manager = new User(1235, "John Williamson", "manager", "jwilliamson@gmail.com");
        $shiftStart = new DateTimeImmutable("2015-10-05 11:00 AM");
        $shiftEnd = new DateTimeImmutable("2015-10-05 4:00 PM");
        $shiftMapper->findShiftsInTimePeriod($weekStart, $weekEnd)->willReturn([
            new Shift(4567, $manager, $currentUser, 0.5, $shiftStart, $shiftEnd),
            new Shift(4568, $manager, $currentUser, 0.5, $shiftStart->modify("+2 days"), $shiftEnd->modify("+2 days")),
            new Shift(4569, $manager, $currentUser, 0.5, $shiftStart->modify("+4 days"), $shiftEnd->modify("+4 days"))
        ]);

        $employeeId = 1234;
        $weekOf = "2015-10-07";
        $payload = $this($currentUser, $employeeId, $weekOf);

        $payload->shouldHaveType(Payload::class);
        $payload->getStatus()->shouldBe(Payload::SUCCESS);
        $payload->getOutput()->shouldHaveType(HoursWorkedSummary::class);
        $payload->getOutput()->getStartDate()->shouldBeLike($weekStart);
        $payload->getOutput()->getEndDate()->shouldBeLike($weekEnd);
        $payload->getOutput()->getTotalHours()->shouldBe(13.5);
    }

    function it_does_not_allow_unauthenticated_access()
    {
        $currentUser = new User(1234, "Richard Roma", "employee", "ricky@roma.com");

        $payload = $this($currentUser, 1234, "2015-10-07");

        $payload->getStatus()->shouldBe(Payload::NOT_AUTHENTICATED);
    }

    function it_does_not_allow_current_user_to_get_hours_summary_of_another_employee()
    {
        $currentUser = new User(1234, "Richard Roma", "employee", "ricky@roma.com");
        $currentUser->authenticate();

        $payload = $this($currentUser, 4321, "2015-10-07");

        $payload->getStatus()->shouldBe(Payload::NOT_AUTHORIZED);
    }

    function it_returns_not_valid_when_date_is_not_valid()
    {
        $currentUser = new User(1234, "Richard Roma", "employee", "ricky@roma.com");
        $currentUser->authenticate();

        $payload = $this($currentUser, 1234, "not a date");

        $payload->getStatus()->shouldBe(Payload::NOT_VALID);
        $payload->getMessages()->shouldHaveKeyWithValue("date", GetHoursWorkedInWeek::INVALID_DATE_MESSAGE);
    }
}
