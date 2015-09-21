<?php

namespace Scheduler\Domain\Model\Shift;

use DateTimeImmutable;
use DateTimeInterface;
use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;

class Shift
{
    private $id;
    private $manager;
    private $employee;
    private $break;
    private $startTime;
    private $endTime;
    private $created;
    private $updated;

    public static function withManagerAndTimes(User $manager, DateTimeInterface $start, DateTimeInterface $end, $break = 0.0)
    {
        $employee = new NullUser();

        return new Shift(null, $manager, $employee, $break, $start, $end);
    }

    public function __construct($id, User $manager, User $employee, $break,
        DateTimeInterface $startTime, DateTimeInterface $endTime,
        DateTimeInterface $created = null, DateTimeInterface $updated = null)
    {
        if (isset($id) && ! is_int($id)) {
            throw new \InvalidArgumentException("The id must be an integer");
        }

        if (! is_float($break)) {
            throw new \InvalidArgumentException("The break must be a float");
        }

        $this->id = $id;
        $this->manager = $manager;
        $this->employee = $employee;
        $this->break = $break;
        $this->startTime = $startTime;
        $this->endTime = $endTime;

        $this->created = isset($created) ? $created : new DateTimeImmutable();
        $this->updated = isset($updated) ? $updated : new DateTimeImmutable();
    }

    public function assignTo(User $employee)
    {
        $shift = clone $this;
        $shift->employee = $employee;
        $shift->updated = new DateTimeImmutable();

        return $shift;
    }

    public function changeStartTime(DateTimeInterface $startTime)
    {
        $shift = clone $this;
        $shift->startTime = $startTime;
        $shift->updated = new DateTimeImmutable();

        return $shift;
    }

    public function changeEndTime(DateTimeInterface $endTime)
    {
        $shift = clone $this;
        $shift->endTime = $endTime;
        $shift->updated = new DateTimeImmutable();

        return $shift;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function getEmployee()
    {
        return $this->employee;
    }

    public function getBreak()
    {
        return $this->break;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function getHours()
    {
        $diff = $this->startTime->diff($this->endTime);

        return $diff->h + ($diff->i / 60) - $this->break;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }
}
