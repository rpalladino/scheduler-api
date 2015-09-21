<?php

namespace Scheduler\Domain\Model\Shift;

use DateTimeInterface;
use Scheduler\Domain\Model\User\User;

interface ShiftMapper
{
    public function find($id);
    public function findOpenShifts();
    public function findShiftsByEmployeeId($employee_id);
    public function findShiftsInTimePeriod(DateTimeInterface $start, DateTimeInterface $end);
    public function findShiftsInTimePeriodByEmployeeId(DateTimeInterface $start, DateTimeInterface $end, $employeeId);
    public function insert(Shift $shift);
    public function update(Shift $shift);
}
