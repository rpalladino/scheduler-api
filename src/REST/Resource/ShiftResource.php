<?php

namespace Scheduler\REST\Resource;

use Scheduler\Domain\Model\Shift\Shift;

class ShiftResource
{

    public function transform(Shift $shift)
    {
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
    }

    public function item(Shift $shift)
    {
        return ["shift" => $this->transform($shift)];
    }

    public function collection(array $shifts)
    {
        return [
            "shifts" => array_map(function (Shift $shift) {
                return $this->transform($shift);
            }, $shifts)
        ];
    }
}
