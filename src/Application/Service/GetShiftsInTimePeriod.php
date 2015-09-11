<?php

namespace Scheduler\Application\Service;

use DateTimeInterface;
use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;

class GetShiftsInTimePeriod
{
    private $payload;
    private $shiftMapper;

    public function __construct(ShiftMapper $shiftMapper)
    {
        $this->payload = new Payload();
        $this->shiftMapper = $shiftMapper;
    }

    public function __invoke(DateTimeInterface $start, DateTimeInterface $end)
    {
        $shifts = $this->shiftMapper->findShiftsInTimePeriod($start, $end);

        return $this->payload->setStatus(Payload::SUCCESS)
                             ->setOutput($shifts);
    }
}
