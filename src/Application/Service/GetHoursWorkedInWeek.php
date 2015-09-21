<?php

namespace Scheduler\Application\Service;

use Aura\Payload\Payload;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Service\HoursWorkedCalculator;

class GetHoursWorkedInWeek
{
    const INVALID_DATE_MESSAGE = "must be a valid string representation of a date";

    private $payload;
    private $calculator;

    public function __construct(HoursWorkedCalculator $calculator)
    {
        $this->payload = new Payload();
        $this->calculator = $calculator;
    }

    public function __invoke(User $currentUser, $employeeId, $weekOf)
    {
        if (! $currentUser->isAuthenticated()) {
            return $this->payload->setStatus(Payload::NOT_AUTHENTICATED);
        }

        if ($currentUser->getId() !== $employeeId) {
            return $this->payload->setStatus(Payload::NOT_AUTHORIZED);
        }

        try {
            $weekOf = new \DateTimeImmutable($weekOf);
        } catch (\Exception $e) {
            return $this->payload->setStatus(Payload::NOT_VALID)
                                 ->setMessages([
                                     "date" => self::INVALID_DATE_MESSAGE
                                 ]);
        }

        $summary = $this->calculator->calculateHoursWorkedInWeek($currentUser, $weekOf);

        return $this->payload->setStatus(Payload::SUCCESS)
                             ->setOutput($summary);
    }
}
