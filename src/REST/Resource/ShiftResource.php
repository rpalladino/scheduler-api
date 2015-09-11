<?php

namespace Scheduler\REST\Resource;

use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\REST\Resource\UserResource;

class ShiftResource
{
    private $userResource;

    public function __construct(UserResource $userResource)
    {
        $this->userResource = $userResource;
    }

    public function transform(Shift $shift)
    {
        return [
            "id" => $shift->getId(),
            "manager" => $this->userResource->transform($shift->getManager()),
            "start_time" => $shift->getStartTime()->format(DATE_RFC2822),
            "end_time" => $shift->getEndTime()->format(DATE_RFC2822),
            "break" => $shift->getBreak(),
            "employee" => $this->userResource->transform($shift->getEmployee())
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
