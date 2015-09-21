<?php

namespace Scheduler\Application\Service;

use Aura\Payload\Payload;
use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Model\User\UserMapper;

class GetEmployee
{
    private $payload;
    private $userMapper;

    public function __construct(UserMapper $userMapper)
    {
        $this->payload = new Payload();
        $this->userMapper = $userMapper;
    }

    public function __invoke(User $currentUser, $employeeId)
    {
        if (! $currentUser->isAuthenticated()) {
            return $this->payload->setStatus(Payload::NOT_AUTHENTICATED);
        }

        $employee = $this->userMapper->find($employeeId);

        if ($employee === null) {
            return $this->payload->setStatus(Payload::NOT_FOUND);
        }

        return $this->payload->setStatus(Payload::SUCCESS)
                             ->setOutput($employee);
    }
}
