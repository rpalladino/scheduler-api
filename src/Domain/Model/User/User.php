<?php

namespace Scheduler\Domain\Model\User;

use DateTimeImmutable as DateTime;
use DateTimeInterface;

class User
{
    private $id;
    private $name;
    private $role;
    private $email;
    private $phone;
    private $created;
    private $updated;

    private $authenticated;

    private static $roles = [
        "employee" => true,
        "manager" => true
    ];

    public static function employeeNamedWithEmail($name, $email)
    {
        return new User(null, $name, "employee", $email);
    }

    public static function managerNamedWithEmail($name, $email)
    {
        return new User(null, $name, "manager", $email);
    }

    public function __construct($id, $name, $role, $email = null, $phone = null,
        DateTimeInterface $created = null, DateTimeInterface $updated = null)
    {
        if (isset($id) && ! is_int($id)) {
            throw new \InvalidArgumentException("The id must be an integer");
        }

        if (empty($email) && empty($phone)) {
            throw new \InvalidArgumentException("At least one of phone or email must be defined");
        }

        if (! isset(self::$roles[$role])) {
            throw new \InvalidArgumentException("The role must be either employee or manager");
        }

        $this->id = $id;
        $this->name = $name;
        $this->role = $role;
        $this->email = $email;
        $this->phone = $phone;

        $this->created = $created ?: new DateTime();
        $this->updated = $updated ?: new DateTime();

        $this->authenticated = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    public function authenticate()
    {
        $this->authenticated = true;
    }
}
