<?php

namespace Scheduler\Infrastructure\Auth;

use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\UserMapper;

class TokenAuthenticator
{
    private $userMapper;

    private $tokenMap;

    public function __construct(UserMapper $userMapper, array $tokenMap)
    {
        $this->userMapper = $userMapper;
        $this->tokenMap = $tokenMap;
    }

    public function getUserForToken($token)
    {
        if (! array_key_exists($token, $this->tokenMap)) {
            return new NullUser();
        }

        $id = $this->tokenMap[$token];
        $user = $this->userMapper->find($id);
        $user->authenticate();

        return $user;
    }
}
