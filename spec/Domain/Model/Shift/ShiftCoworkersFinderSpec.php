<?php

namespace spec\Scheduler\Domain\Model\Shift;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class ShiftCoworkersFinderSpec extends ObjectBehavior
{
    function let(ShiftMapper $shiftMapper)
    {
        $this->beConstructedWith($shiftMapper);
    }

    function it_can_find_coworkers_for_shift($shiftMapper)
    {
        $employee = new User(12346, "Shelly Levene", "employee", "oldguy@aol.com");
        $manager = new User(12345, "John Williamson", "manager", "john@abc.com");
        $start = new DateTime("10:30 AM");
        $end = new DateTime("1:30 PM");
        $shift = new Shift(76543, $manager, $employee, 0.5, $start, $end);

        $coworker = new User(12347, "Richard Roma", "employee", "ricky@roma.com");
        $coworkerShift = new Shift(76544, $manager, $coworker, 0.5, $start, $end);

        $shiftMapper->findShiftsInTimePeriod($start, $end)->willReturn([
            $shift,
            $coworkerShift
        ]);

        $result = $this->findCoworkersForShift($shift);

        $result->shouldBeArray();
        $result->shouldContain($coworkerShift->getEmployee());
    }
}
