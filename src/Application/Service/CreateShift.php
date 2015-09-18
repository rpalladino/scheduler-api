<?php

namespace Scheduler\Application\Service;

use DateTime;
use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\NullUser;

class CreateShift
{
    const INVALID_DATE_MESSAGE = "must be a valid string representation of a date";

    private $payload;
    private $shiftMapper;

    public function __construct(ShiftMapper $shiftMapper)
    {
        $this->payload = new Payload();
        $this->shiftMapper = $shiftMapper;
    }

    public function __invoke($currentUser, $start, $end, $break)
    {
        if (! $currentUser->isAuthenticated()) {
            return $this->payload->setStatus(Payload::NOT_AUTHENTICATED);
        }

        if ($currentUser->getRole() !== "manager") {
            return $this->payload->setStatus(Payload::NOT_AUTHORIZED);
        }

        $invalid = [];
        try {
            $start = new DateTime($start);
        } catch (\Exception $e) {
            $invalid["start"] = self::INVALID_DATE_MESSAGE;
        }

        try {
            $end = new DateTime($end);
        } catch (\Exception $e) {
            $invalid["end"] = self::INVALID_DATE_MESSAGE;
        }

        if (count($invalid)) {
            return $this->payload->setStatus(Payload::NOT_VALID)
                                 ->setMessages($invalid);
        }

        $employee = new NullUser();
        $break = (float) $break;

        $shift = new Shift(null, $currentUser, $employee, $break, $start, $end);
        $this->shiftMapper->insert($shift);

        $this->payload->setStatus(Payload::CREATED);
        $this->payload->setOutput($shift);

        return $this->payload;
    }
}
