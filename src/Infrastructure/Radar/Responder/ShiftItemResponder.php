<?php

namespace Scheduler\Infrastructure\Radar\Responder;

use Scheduler\Domain\Model\User\User;
use Scheduler\REST\Resource\ShiftResource;
use Scheduler\REST\Resource\UserResource;

class ShiftItemResponder extends ResourceResponder
{
    private $shiftResource;
    private $userResource;

    public function __construct(ShiftResource $shiftResource, UserResource $userResource)
    {
        $this->shiftResource = $shiftResource;
        $this->userResource = $userResource;
    }

    protected function success()
    {
        $this->response = $this->response->withStatus(200);
        extract($this->payload->getOutput());

        $item = $this->shiftResource->item($shift);

        if (isset($coworkers)) {
            $item["shift"]["coworkers"] = array_map(function (User $coworker) {
                return $this->userResource->transform($coworker);
            }, $coworkers);
        }

        $this->jsonBody($item);
    }
}
