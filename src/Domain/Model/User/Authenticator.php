<?php

namespace Scheduler\Domain\Model\User;

interface Authenticator
{
    public function getUserForToken($token);
}
