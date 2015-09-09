<?php

namespace Scheduler\Infrastructure\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

class DbalSchema
{
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function create()
    {
        $sm = $this->connection->getSchemaManager();
        $schema = new Schema();

        $users = $schema->createTable("users");
        $users->addColumn("id", "integer", ["unsigned" => true, "autoincrement" => true]);
        $users->addColumn("name", "string", ["length" => 32]);
        $users->addColumn("role", "string", ["length" => 8]);
        $users->addColumn("email", "string", ["length" => 254, "notnull" => false]);
        $users->addColumn("phone", "string", ["length" => 12, "notnull" => false]);
        $users->addColumn("created_at", "datetime");
        $users->addColumn("updated_at", "datetime");
        $users->setPrimaryKey(["id"]);
        $sm->createTable($users);

        $shifts = $schema->createTable("shifts");
        $shifts->addColumn("id", "integer", ["unsigned" => true, "autoincrement" => true]);
        $shifts->addColumn("manager_id", "integer", ["unsigned" => true]);
        $shifts->addColumn("employee_id", "integer", ["unsigned" => true, "notnull" => false]);
        $shifts->addColumn("break", "float");
        $shifts->addColumn("start_time", "datetime");
        $shifts->addColumn("end_time", "datetime");
        $shifts->addColumn("created_at", "datetime");
        $shifts->addColumn("updated_at", "datetime");
        $shifts->setPrimaryKey(["id"]);
        $shifts->addForeignKeyConstraint($users, ["manager_id"], ["id"], ["onUpdate" => "CASCADE"]);
        $shifts->addForeignKeyConstraint($users, ["employee_id"], ["id"], ["onUpdate" => "CASCADE"]);
        $sm->createTable($shifts);
    }

    public function drop()
    {
        $sm = $this->connection->getSchemaManager();

        if ($sm->tablesExist(["shifts"])) {
            $sm->dropTable("shifts");
        }

        if ($sm->tablesExist(["users"])) {
            $sm->dropTable("users");
        }
    }
}
