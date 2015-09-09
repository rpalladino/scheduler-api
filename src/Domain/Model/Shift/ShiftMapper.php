<?php

namespace Scheduler\Domain\Model\Shift;

use DateTimeInterface;
use Scheduler\Domain\Model\User\User;

interface ShiftMapper
{
    public function find($id);
    public function findOpenShifts();
    public function findShiftsAssignedTo(User $employee);
    public function findShiftsInTimePeriod(DateTimeInterface $start, DateTimeInterface $end);
    public function insert(Shift $shift);
    public function update(Shift $shift);
}
