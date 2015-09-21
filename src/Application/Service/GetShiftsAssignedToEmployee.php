<?php

namespace Scheduler\Application\Service;

use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class GetShiftsAssignedToEmployee
{
    const INVALID_ID_MESSAGE = "must be a valid employee id";

    private $payload;
    private $shiftMapper;

    public function __construct(ShiftMapper $shiftMapper)
    {
        $this->payload = new Payload();
        $this->shiftMapper = $shiftMapper;
    }

    public function __invoke(User $currentUser, $employeeId)
    {
        if (! $currentUser->isAuthenticated()) {
            return $this->payload->setStatus(Payload::NOT_AUTHENTICATED);
        }

        if (! is_integer($employeeId)) {
            return $this->payload->setStatus(Payload::NOT_VALID)
                                 ->setMessages(["employee_id" => self::INVALID_ID_MESSAGE]);
        }

        if ($currentUser->getId() !== $employeeId) {
            return $this->payload->setStatus(Payload::NOT_AUTHORIZED);
        }

        $shifts = $this->shiftMapper->findShiftsByEmployeeId($currentUser->getId());

        return $this->payload->setStatus(Payload::SUCCESS)
                             ->setOutput($shifts);
    }
}
