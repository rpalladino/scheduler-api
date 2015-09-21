<?php

namespace Scheduler\Domain\Model\Shift;

use DateTimeInterface;

class HoursWorkedSummary
{
    private $startDate;
    private $endDate;
    private $hours;

    public function __construct(DateTimeInterface $start, DateTimeInterface $end, $hours)
    {
        $this->startDate = $start;
        $this->endDate = $end;
        $this->hours = $hours;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getTotalHours()
    {
        return $this->hours;
    }
}
