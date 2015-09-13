<?php

namespace Scheduler\Application\Service;

use DateTime;
use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class GetShiftsInTimePeriod
{
    const INVALID_DATE_MESSAGE = "must be a valid string representation of a date";

    private $payload;
    private $shiftMapper;

    public function __construct(ShiftMapper $shiftMapper)
    {
        $this->payload = new Payload();
        $this->shiftMapper = $shiftMapper;
    }

    public function __invoke(User $user, $start, $end)
    {
        if (! $user->isAuthenticated()) {
            return $this->payload->setStatus(Payload::NOT_AUTHENTICATED);
        }

        if ($user->getRole() !== "manager") {
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
            return $this->payload
                            ->setStatus(Payload::NOT_VALID)
                            ->setInput([$start, $end])
                            ->setMessages($invalid);
        }

        $shifts = $this->shiftMapper->findShiftsInTimePeriod($start, $end);

        return $this->payload->setStatus(Payload::SUCCESS)
                             ->setOutput($shifts);
    }
}
