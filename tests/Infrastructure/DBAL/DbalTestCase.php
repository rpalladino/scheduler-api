<?php

namespace Scheduler\Test\Infrastructure\DBAL;

use Doctrine\DBAL\DriverManager;
use PHPUnit_Extensions_Database_DataSet_YamlDataSet as YamlDataSet;
use Scheduler\Infrastructure\DBAL\DbalSchema;

abstract class DbalTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    static private $dbalConnection = null;
    private $connection;

    /**
     * @beforeClass
     */
    public static function setupDatabase()
    {
        self::$dbalConnection = DriverManager::getConnection([
            'dbname' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
            'driver' => getenv('DB_DRIVER'),
        ]);

        self::createSchema(self::$dbalConnection);
    }

    /**
     * @return Doctrine\DBAL\Connection
     */
    public static function getDbalConnection()
    {
        return self::$dbalConnection;
    }

    private static function createSchema($connection)
    {
        $schema = new DbalSchema($connection);
        $schema->drop();
        $schema->create();
    }

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        if (! isset($this->connection)) {
            $pdo = self::getDbalConnection()->getWrappedConnection();
            $this->connection = $this->createDefaultDBConnection($pdo, getenv('DB_NAME'));
        }

        return $this->connection;
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return new YamlDataSet(__DIR__ . "/fixtures.yml");
    }
}
