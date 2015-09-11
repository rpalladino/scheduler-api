<?php

namespace Scheduler\Infrastructure\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Doctrine\DBAL\DriverManager;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\UserMapper;
use Scheduler\Infrastructure\DBAL;

class ServiceConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        $di->set('db.connection', $di->lazy(
            [DriverManager::class, 'getConnection'],
            [
                'dbname' => getenv('DB_NAME'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'host' => getenv('DB_HOST'),
                'driver' => getenv('DB_DRIVER'),
            ]
        ));

        $di->set('db.schema', $di->lazyNew(DBAL\DbalSchema::class));
        $di->params[DBAL\DbalSchema::class]['connection'] = $di->lazyGet('db.connection');

        $di->set("user.mapper", $di->lazyNew(DBAL\DbalUserMapper::class));
        $di->params[DBAL\DbalUserMapper::class]['db'] = $di->lazyGet('db.connection');

        $di->set("shift.mapper", $di->lazyNew(DBAL\DbalShiftMapper::class));
        $di->params[DBAL\DbalShiftMapper::class]['db'] = $di->lazyGet('db.connection');
        $di->params[DBAL\DbalShiftMapper::class]['userMapper'] = $di->lazyGet("user.mapper");
    }
}
