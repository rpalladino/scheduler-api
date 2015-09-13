<?php

namespace Scheduler\Domain\Model\User;

class NullUser extends User
{
    public function __construct()
    {
    }

    public function isAuthenticated()
    {
        return false;
    }
}
