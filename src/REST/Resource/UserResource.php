<?php

namespace Scheduler\REST\Resource;

use Scheduler\Domain\Model\User\User;

class UserResource
{

    public function transform(User $user)
    {
        return [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "phone" => $user->getPhone()
        ];
    }
}
