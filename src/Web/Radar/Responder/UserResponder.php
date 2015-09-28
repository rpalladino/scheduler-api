<?php

namespace Scheduler\Web\Radar\Responder;

use Scheduler\REST\Resource\UserResource;

class UserResponder extends ResourceResponder
{
    public function __construct(UserResource $resource)
    {
        $this->resource = $resource;
    }
}
