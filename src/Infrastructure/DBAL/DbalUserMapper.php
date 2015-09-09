<?php

namespace Scheduler\Infrastructure\DBAL;

use DateTime;
use Doctrine\DBAL\Statement;
use Doctrine\DBAL\Types\Type;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Model\User\UserMapper;

class DbalUserMapper extends DbalMapper implements UserMapper
{
    const COLUMNS = "id, name, role, email, phone, created_at, updated_at";

    protected static function findStatement()
    {
        return sprintf(
            "SELECT %s FROM users WHERE id = ?",
            self::COLUMNS
        );
    }

    protected static function findByRoleStatement()
    {
        return sprintf(
            "SELECT %s FROM users WHERE role = ?",
            self::COLUMNS
        );
    }

    protected static function insertStatement()
    {
        return "INSERT INTO users VALUES (?, ?, ?, ?, ?, ?, ?)";
    }

    public function find($id)
    {
        if (! is_int($id)) {
            throw new \InvalidArgumentException("The id must be an integer");
        }

        return $this->abstractFind($id);
    }

    public function findByRole($role)
    {
        $statement = $this->db->prepare(self::findByRoleStatement());
        $statement->bindValue(1, $role, \PDO::PARAM_STR);
        $statement->execute();

        return $this->loadAll($statement);
    }

    protected function doLoad($id, array $resultSet)
    {
        extract($resultSet);
        $id = (int) $id;
        $created = new DateTime($created_at);
        $updated = new DateTime($updated_at);

        return new User($id, $name, $role, $email, $phone, $created, $updated);
    }

    public function insert(User $user)
    {
        return $this->abstractInsert($user);
    }

    /**
     * Bind query parameters and execute insert statement
     *
     * @param  User      $subject
     * @param  Statement $insertStatement
     *
     * @return int                        The id of the inserted row
     */
    protected function doInsert($subject, Statement $insertStatement)
    {
        $insertStatement->bindValue(1, null);
        $insertStatement->bindValue(2, $subject->getName(), \PDO::PARAM_STR);
        $insertStatement->bindValue(3, $subject->getRole(), \PDO::PARAM_STR);
        $insertStatement->bindValue(4, $subject->getEmail(), \PDO::PARAM_STR);
        $insertStatement->bindValue(5, $subject->getPhone(), \PDO::PARAM_STR);
        $insertStatement->bindValue(6, $subject->getCreated(), Type::DATETIME);
        $insertStatement->bindValue(7, $subject->getUpdated(), Type::DATETIME);
        $insertStatement->execute();
    }
}
