<?php

namespace Scheduler\Domain\Model\Shift;

use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;

class ShiftCoworkersFinder
{
    private $shiftMapper;

    public function __construct(ShiftMapper $shiftMapper)
    {
        $this->shiftMapper = $shiftMapper;
    }

    public function findCoworkersForShift(Shift $shift)
    {
        $shifts = $this->shiftMapper->findShiftsInTimePeriod(
            $shift->getStartTime(),
            $shift->getEndTime()
        );

        $employee = $shift->getEmployee();

        $shifts = array_filter($shifts, function ($shift) use ($employee) {
            return $shift->getEmployee()->getId() !== $employee->getId();
        });

        $coworkers = array_map(function ($shift) {
            return $shift->getEmployee();
        }, $shifts);

        return array_values($coworkers);
    }
}
