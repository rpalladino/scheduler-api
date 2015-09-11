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
                             ->setOutput(["shifts" => $this->transform($shifts)]);
    }

    private function transform(array $shifts)
    {
        return array_map(function (Shift $shift) {
            return [
                "id" => $shift->getId(),
                "manager" => [
                    "id" => $shift->getManager()->getId(),
                    "name" => $shift->getManager()->getName(),
                    "email" => $shift->getManager()->getEmail(),
                    "phone" => $shift->getManager()->getPhone()
                ],
                "start_time" => $shift->getStartTime()->format(DATE_RFC2822),
                "end_time" => $shift->getEndTime()->format(DATE_RFC2822),
                "break" => $shift->getBreak(),
                "employee" => [
                    "id" => $shift->getEmployee()->getId(),
                    "name" => $shift->getEmployee()->getName(),
                    "email" => $shift->getEmployee()->getEmail(),
                    "phone" => $shift->getEmployee()->getPhone()
                ]
            ];
        }, $shifts);
    }
}
