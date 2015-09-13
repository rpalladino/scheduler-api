<?php

namespace Scheduler\Infrastructure\Radar\Config;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Aura\Payload\Payload;
use Symfony\Component\Yaml\Parser;

class SeedConfig extends ContainerConfig
{
    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        // For demonstration purposes, seed database using test fixtures
        $adr->get('seed', "/seed", function () use ($di) {
            $payload = new Payload();

            $di->get('db.schema')->drop();
            $di->get('db.schema')->create();

            $yaml = new Parser();
            $yamlFile = __DIR__ . "/../../../../tests/Infrastructure/DBAL/fixtures.yml";
            $fixtures = $yaml->parse(file_get_contents($yamlFile));

            foreach ($fixtures as $table => $rows) {
                foreach ($rows as $row) {
                    $di->get('db.connection')->insert($table, $row);
                }
            }

            $payload->setStatus($payload::SUCCESS);
            $payload->setOutput(["message" => "Database seeded"]);

            return $payload;
        });
    }
}
