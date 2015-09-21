<?php

namespace spec\Scheduler\Domain\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTimeImmutable;
use Scheduler\Domain\Model\Shift\HoursWorkedSummary;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class HoursWorkedCalculatorSpec extends ObjectBehavior
{
    function let(ShiftMapper $shiftMapper)
    {
        $this->beConstructedWith($shiftMapper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Domain\Service\HoursWorkedCalculator');
    }

    function it_calculates_hours_worked_in_week($shiftMapper)
    {
        $employee = new User(1234, "Richard Roma", "employee", "ricky@roma.com");
        $manager = new User(1235, "John Williamson", "manager", "jwilliamson@gmail.com");
        $weekStart = new DateTimeImmutable("2015-10-05");
        $weekEnd = new DateTimeImmutable("2015-10-11");
        $shiftStart = new DateTimeImmutable("2015-10-05 11:00 AM");
        $shiftEnd = new DateTimeImmutable("2015-10-05 4:00 PM");
        $shiftMapper->findShiftsInTimePeriodByEmployeeId($weekStart, $weekEnd, 1234)->willReturn([
            new Shift(4567, $manager, $employee, 0.5, $shiftStart, $shiftEnd),
            new Shift(4568, $manager, $employee, 0.5, $shiftStart->modify("+2 days"), $shiftEnd->modify("+2 days")),
            new Shift(4569, $manager, $employee, 0.5, $shiftStart->modify("+4 days"), $shiftEnd->modify("+4 days"))
        ]);

        $weekOf = new DateTimeImmutable("2015-10-07");
        $result = $this->calculateHoursWorkedInWeek($employee, $weekOf);

        $result->shouldHaveType(HoursWorkedSummary::class);
        $result->getStartDate()->shouldBeLike($weekStart);
        $result->getEndDate()->shouldBeLike($weekEnd);
        $result->getTotalHours()->shouldBe(13.5);
    }
}
