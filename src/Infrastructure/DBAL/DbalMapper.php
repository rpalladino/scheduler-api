<?php

namespace Scheduler\Infrastructure\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;

abstract class DbalMapper
{
    protected $db;
    protected $loadedMap;

    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->loadedMap = [];
    }

    protected function abstractFind($id)
    {
        if (isset($this->loadedMap[$id])) {
            return $this->loadedMap[$id];
        }

        $findStatement = $this->db->prepare($this->findStatement());
        $findStatement->bindValue(1, $id, \PDO::PARAM_INT);
        $findStatement->execute();
        $resultSet = $findStatement->fetch();

        return $this->load($resultSet);
    }

    protected function abstractInsert($subject)
    {
        $insertStatement = $this->db->prepare(static::insertStatement());
        $this->doInsert($subject, $insertStatement);

        $id = (int) $this->db->lastInsertId();
        $this->setPropertyValue($subject, "id", $id);

        $this->loadedMap[$id] = $subject;

        return $id;
    }

    public function clean()
    {
        $this->loadedMap = [];
    }

    /**
     * Bind query parameters and execute insert statement
     *
     * @param            $subject
     * @param  Statement $insertStatement
     *
     * @return int                        The id of the inserted row
     */
    abstract protected function doInsert($subject, Statement $insertStatement);

    abstract protected function doLoad($id, array $resultSet);

    abstract protected static function findStatement();

    abstract protected static function insertStatement();

    protected function load(array $resultSet)
    {
        $id = $resultSet["id"];

        if (isset($this->loadedMap[$id])) {
            return $this->loadedMap[$id];
        }

        $result = $this->doLoad($id, $resultSet);
        $this->loadedMap[$id] = $result;

        return $result;
    }

    protected function loadAll(Statement $statement)
    {
        $result = [];
        foreach ($statement as $row) {
            $result[] = $this->load($row);
        }

        return $result;
    }

    protected function setPropertyValue($subject, $name, $value)
    {
        $reflector = new \ReflectionClass($subject);

        $property = $reflector->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($subject, $value);
    }
}
