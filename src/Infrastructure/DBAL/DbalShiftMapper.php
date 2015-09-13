<?php

namespace Scheduler\Infrastructure\DBAL;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOStatement;
use Doctrine\DBAL\Statement;
use Doctrine\DBAL\Types\Type;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Model\User\UserMapper;

class DbalShiftMapper extends DbalMapper implements ShiftMapper
{
    const SHIFT_COLUMNS = "s.id, s.manager_id, s.employee_id, s.break,
                           s.start_time, s.end_time, s.created_at, s.updated_at";

    const MANAGER_COLUMNS = "m.name as m_name, m.role as m_role, m.email as m_email,
                             m.phone as m_phone, m.created_at as m_created_at,
                             m.updated_at as m_updated_at";

    const EMPLOYEE_COLUMNS = "e.name as e_name, e.role as e_role, e.email as e_email,
                              e.phone as e_phone, e.created_at as e_created_at,
                              e.updated_at as e_updated_at";

    protected static function findStatement()
    {
        return sprintf(self::findWithAssociations(), "s.id = ?");
    }

    protected static function findAssignedToStatement()
    {
        return sprintf(self::findWithAssociations(), "s.employee_id = ?");
    }

    protected static function findInTimePeriodStatement()
    {
        return sprintf(
            self::findWithAssociations(),
            "(start_time BETWEEN :start AND :end
                OR end_time BETWEEN :start and :end)"
        );
    }

    protected static function findOpenShiftsStatement()
    {
        return sprintf(self::findWithManagerAssociation(), "s.employee_id is null");
    }

    protected static function findWithManagerAssociation()
    {
        return sprintf(
            "SELECT %s, %s
             FROM shifts s, users m
             WHERE %%s and s.manager_id = m.id",
            self::SHIFT_COLUMNS,
            self::MANAGER_COLUMNS
        );
    }

    protected static function findWithAssociations()
    {
        return sprintf(
            "SELECT %s, %s, %s
             FROM shifts s
                LEFT JOIN users m ON (s.manager_id = m.id)
                LEFT JOIN users e on (s.employee_id = e.id)
             WHERE %%s",
            self::SHIFT_COLUMNS,
            self::MANAGER_COLUMNS,
            self::EMPLOYEE_COLUMNS
        );
    }

    protected static function insertStatement()
    {
        return "INSERT INTO shifts VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    }

    protected static function updateStatement()
    {
        return "UPDATE shifts
                SET employee_id = ?, start_time = ?, end_time = ?, updated_at = ?
                WHERE id = ?";
    }

    public function __construct(Connection $db, UserMapper $userMapper)
    {
        parent::__construct($db);
        $this->userMapper = $userMapper;
    }

    public function find($id)
    {
        if (! is_int($id)) {
            throw new \InvalidArgumentException("The id must be an integer");
        }

        return $this->abstractFind($id);
    }

    public function findOpenShifts()
    {
        $statement = $this->db->prepare(self::findOpenShiftsStatement());
        $statement->execute();

        return $this->loadAll($statement);
    }

    public function findShiftsAssignedTo(User $employee)
    {
        $statement = $this->db->prepare(self::findAssignedToStatement());
        $statement->bindValue(1, $employee->getId(), \PDO::PARAM_INT);
        $statement->execute();

        return $this->loadAll($statement);
    }

    public function findShiftsInTimePeriod(DateTimeInterface $start, DateTimeInterface $end)
    {
        $statement = $this->db->prepare(self::findInTimePeriodStatement());
        $statement->bindValue(":start", $start, Type::DATETIME);
        $statement->bindValue(":end", $end, Type::DATETIME);
        $statement->execute();

        return $this->loadAll($statement);
    }

    protected function doLoad($id, array $resultSet)
    {
        $id = (int) $resultSet["id"];
        $break = (float) $resultSet["break"];
        $start = new DateTimeImmutable($resultSet["start_time"]);
        $end = new DateTimeImmutable($resultSet["end_time"]);
        $created = new DateTimeImmutable($resultSet["created_at"]);
        $updated = new DateTimeImmutable($resultSet["updated_at"]);

        $manager = $this->userMapper->load([
            "id" => $resultSet["manager_id"],
            "name" => $resultSet["m_name"],
            "role" => $resultSet["m_role"],
            "email" => $resultSet["m_email"],
            "phone" => $resultSet["m_phone"],
            "created_at" => $resultSet["m_created_at"],
            "updated_at" => $resultSet["m_updated_at"]
        ]);

        $employee = isset($resultSet["employee_id"])
            ? $this->userMapper->load([
                "id" => $resultSet["employee_id"],
                "name" => $resultSet["e_name"],
                "role" => $resultSet["e_role"],
                "email" => $resultSet["e_email"],
                "phone" => $resultSet["e_phone"],
                "created_at" => $resultSet["e_created_at"],
                "updated_at" => $resultSet["e_updated_at"]
              ])
            : new NullUser();

        return new Shift($id, $manager, $employee, $break, $start, $end, $created, $updated);
    }

    /**
     * Insert a Shift instance into the database.
     *
     * @param  Shift  $shift The Shift instance to insert
     *
     * @return int           The id of the inserted Shift
     */
    public function insert(Shift $shift)
    {
        return $this->abstractInsert($shift);
    }

    /**
     * Bind query parameters and execute insert statement
     *
     * @param  Shift     $shift
     * @param  Statement $insertStatement The prepared insert statement
     *
     * @return int                        The id of the inserted row
     */
    protected function doInsert($shift, Statement $insertStatement)
    {
        $insertStatement->bindValue(1, null);
        $insertStatement->bindValue(2, $shift->getManager()->getId(), \PDO::PARAM_INT);
        $insertStatement->bindValue(3, $shift->getEmployee()->getId(), \PDO::PARAM_INT);
        $insertStatement->bindValue(4, $shift->getBreak(), \PDO::PARAM_STR);
        $insertStatement->bindValue(5, $shift->getStartTime(), Type::DATETIME);
        $insertStatement->bindValue(6, $shift->getEndTime(), Type::DATETIME);
        $insertStatement->bindValue(7, $shift->getCreated(), Type::DATETIME);
        $insertStatement->bindValue(8, $shift->getUpdated(), Type::DATETIME);
        $insertStatement->execute();
    }

    /**
     * Update a Shift instance
     *
     * @param  Shift  $shift
     *
     * @return void
     */
    public function update(Shift $shift)
    {
        $updateStatement = $this->db->prepare(self::updateStatement());
        $updateStatement->bindValue(1, $shift->getEmployee()->getId(), \PDO::PARAM_INT);
        $updateStatement->bindValue(2, $shift->getStartTime(), Type::DATETIME);
        $updateStatement->bindValue(3, $shift->getEndTime(), Type::DATETIME);
        $updateStatement->bindValue(4, new DateTimeImmutable(), Type::DATETIME);
        $updateStatement->bindValue(5, $shift->getId(), \PDO::PARAM_INT);
        $updateStatement->execute();
    }
}
