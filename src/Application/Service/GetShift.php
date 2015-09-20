<?php

namespace Scheduler\Application\Service;

use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;

class GetShift
{
    private $payload;

    public function __construct(ShiftMapper $shiftMapper)
    {
        $this->payload = new Payload();
        $this->shiftMapper = $shiftMapper;
    }

    public function __invoke($currentUser, $shiftId, $withCoworkers)
    {
        if (! $currentUser->isAuthenticated()) {
            return $this->payload->setStatus(Payload::NOT_AUTHENTICATED);
        }

        $output = [];

        $shift = $this->shiftMapper->find($shiftId);

        if ($shift === null) {
            return $this->payload->setStatus(Payload::NOT_FOUND);
        }

        $output["shift"] = $shift;

        if ($withCoworkers) {
            $output["coworkers"] = $this->findCoworkersForShift($shift);
        }

        return $this->payload->setStatus(Payload::SUCCESS)
                             ->setOutput($output);
    }

    protected function findCoworkersForShift(Shift $shift)
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
