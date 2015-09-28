<?php

namespace Scheduler\Web\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Doctrine\DBAL\DriverManager;
use Scheduler\Domain\Model\Shift\HoursWorkedCalculator;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\UserMapper;
use Scheduler\Domain\Model\User\InMemoryAuthenticator;
use Scheduler\Infrastructure\DBAL;
use Scheduler\REST\Resource\HoursWorkedSummaryResource;
use Scheduler\REST\Resource\ShiftResource;
use Scheduler\REST\Resource\UserResource;

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
                'port' => getenv('DB_PORT'),
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

        $di->set("user.authenticator", $di->lazyNew(InMemoryAuthenticator::class));
        $di->params[InMemoryAuthenticator::class]["userMapper"] = $di->lazyGet("user.mapper");
        $di->params[InMemoryAuthenticator::class]["tokenMap"] = [
            "i_am_a_manager" => 1,
            "i_am_an_employee" => 2,
            "i_am_shelly" => 3
        ];

        $di->set("shift.resource", $di->lazyNew(ShiftResource::class));
        $di->params[ShiftResource::class]["userResource"] = $di->lazyGet("user.resource");

        $di->set("user.resource", $di->lazyNew(UserResource::class));

        $di->set("summary.resource", $di->lazyNew(HoursWorkedSummaryResource::class));

        $di->set("hours.calculator", $di->lazyNew(HoursWorkedCalculator::class));
        $di->params[HoursWorkedCalculator::class]["shiftMapper"] = $di->lazyGet("shift.mapper");
    }
}
