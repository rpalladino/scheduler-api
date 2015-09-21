<?php

namespace Scheduler\Domain\Service;

use DateTimeInterface;
use DateTimeImmutable;
use Scheduler\Domain\Model\Shift\HoursWorkedSummary;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class HoursWorkedCalculator
{
    const DAY_OF_WEEK_START = 1;
    const DAY_OF_WEEK_END = 7;

    private $shiftMapper;

    public function __construct(ShiftMapper $shiftMapper)
    {
        $this->shiftMapper = $shiftMapper;
    }

    public function calculateHoursWorkedInWeek(User $employee, DateTimeInterface $weekOf)
    {
        list($start, $end) = $this->getWeekStartAndEndDates($weekOf);

        $shifts = $this->shiftMapper->findShiftsInTimePeriodByEmployeeId($start, $end, $employee->getId());
        $hours = array_reduce($shifts, function ($total, $shift) {
            return $total + $shift->getHours();
        });

        return new HoursWorkedSummary($start, $end, $hours);
    }

    private function getWeekStartAndEndDates(DateTimeInterface $weekOf)
    {
        $year = $weekOf->format('Y');
        $week = $weekOf->format('W');
        $date = new DateTimeImmutable("00:00:00");

        $start = $date->setISODate($year, $week, self::DAY_OF_WEEK_START);
        $end = $date->setISODate($year, $week, self::DAY_OF_WEEK_END);

        return [$start, $end];
    }
}
