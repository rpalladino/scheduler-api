<?php

namespace Scheduler\Domain\Model\User;

interface UserMapper
{
    public function find($id);
    public function findByRole($role);
    public function insert(User $user);
}
