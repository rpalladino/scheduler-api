<?php

namespace Scheduler\Application\Service;

use DateTime;
use DateTimeInterface;
use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Model\User\UserMapper;

class UpdateShift
{
    const INVALID_DATE_MESSAGE = "must be a valid string representation of a date";
    const SHIFT_NOT_FOUND_MESSAGE = "There is no shift for the specified id";
    const USER_NOT_FOUND_MESSAGE = "There is no employee for the specified id";

    private $payload;
    private $shiftMapper;
    private $userMapper;

    public function __construct(ShiftMapper $shiftMapper, UserMapper $userMapper)
    {
        $this->payload = new Payload();
        $this->shiftMapper = $shiftMapper;
        $this->userMapper = $userMapper;
    }

    public function __invoke(User $currentUser, $shiftId, $employeeId, $start, $end, $break) {
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
            return $this->payload
                            ->setStatus(Payload::NOT_VALID)
                            ->setInput([$start, $end])
                            ->setMessages($invalid);
        }

        $shift = $this->shiftMapper->find($shiftId);

        if ($shift === null) {
            return $this->payload->setStatus(Payload::NOT_FOUND)
                                 ->setMessages(self::SHIFT_NOT_FOUND_MESSAGE);
        }

        if ($employeeId !== null) {
            $employee = $this->userMapper->find($employeeId);

            if ($employee instanceof NullUser) {
                return $this->payload->setStatus(Payload::NOT_FOUND)
                                     ->setMessages(self::USER_NOT_FOUND_MESSAGE);
            }

            $shift = $shift->assignTo($employee);
        }

        $shift = $shift->changeStartTime($start)
                       ->changeEndTime($end);

        $this->shiftMapper->update($shift);

        return $this->payload->setStatus(Payload::UPDATED)
                             ->setOutput($shift);
    }
}
