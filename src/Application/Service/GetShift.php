<?php

namespace Scheduler\Application\Service;

use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftCoworkersFinder;
use Scheduler\Domain\Model\Shift\ShiftMapper;

class GetShift
{
    private $payload;
    private $shiftMapper;
    private $shiftCoworkersFinder;

    public function __construct(
        ShiftMapper $shiftMapper,
        ShiftCoworkersFinder $shiftCoworkersFinder
    ) {
        $this->payload = new Payload();
        $this->shiftMapper = $shiftMapper;
        $this->shiftCoworkersFinder = $shiftCoworkersFinder;
    }

    public function __invoke($currentUser, $shiftId, $withCoworkers)
    {
        if (! $currentUser->isAuthenticated()) {
            return $this->payload->setStatus(Payload::NOT_AUTHENTICATED);
        }

        $shift = $this->shiftMapper->find($shiftId);

        if ($shift === null) {
            return $this->payload->setStatus(Payload::NOT_FOUND);
        }

        if ($withCoworkers) {
            $shift = $shift->withCoworkers(
                $this->shiftCoworkersFinder->findCoworkersForShift($shift)
            );
        }

        return $this->payload->setStatus(Payload::SUCCESS)
                             ->setOutput($shift);
    }
}
