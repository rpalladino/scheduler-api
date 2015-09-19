<?php

namespace Scheduler\Application\Service;

use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Model\User\UserMapper;

class UpdateShift
{
    const SHIFT_NOT_FOUND_MESSAGE = "There is no shift for the specified id";
    const USER_NOT_FOUND_MESSAGE = "There is no employee for the specified id";

    private $payload;
    private $shiftMapper;

    public function __construct(ShiftMapper $shiftMapper, UserMapper $userMapper)
    {
        $this->payload = new Payload();
        $this->shiftMapper = $shiftMapper;
        $this->userMapper = $userMapper;
    }

    public function __invoke(User $currentUser, $shiftId, $employeeId)
    {
        if (! $currentUser->isAuthenticated()) {
            return $this->payload->setStatus(Payload::NOT_AUTHENTICATED);
        }

        if ($currentUser->getRole() !== "manager") {
            return $this->payload->setStatus(Payload::NOT_AUTHORIZED);
        }

        $shift = $this->shiftMapper->find($shiftId);

        if ($shift === null) {
            return $this->payload->setStatus(Payload::NOT_FOUND)
                                 ->setMessages(self::SHIFT_NOT_FOUND_MESSAGE);
        }

        $employee = $this->userMapper->find($employeeId);

        if ($employee instanceof NullUser) {
            return $this->payload->setStatus(Payload::NOT_FOUND)
                                 ->setMessages(self::USER_NOT_FOUND_MESSAGE);
        }

        $shift = $shift->assignTo($employee);
        $this->shiftMapper->update($shift);

        return $this->payload->setStatus(Payload::UPDATED)
                             ->setOutput($shift);
    }
}
