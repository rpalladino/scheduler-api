<?php

namespace Scheduler\Domain\Model\User;

interface Authenticator
{
    public function authenticate($token);
}
