<?php

use Aura\Di\ContainerBuilder;
use Dotenv\Dotenv;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\User\User;
use Scheduler\Infrastructure\Radar\Config\ServiceConfig;

trait SchedulerApiCommon
{
    private $restApiBrowser;
    private $jsonInspector;

    private $container;

    private $shiftMapper;
    private $userMapper;

    private $employee;
    private $manager;

    public function initialize($restApiBrowser, $jsonInspector)
    {
        $this->restApiBrowser = $restApiBrowser;
        $this->jsonInspector = $jsonInspector;

        $dotenv = new Dotenv(__DIR__ . "/../../");
        $dotenv->load();

        $builder = new ContainerBuilder();
        $this->container = $builder->newConfiguredInstance([ServiceConfig::class]);

        $this->shiftMapper = $this->container->get("shift.mapper");
        $this->userMapper = $this->container->get("user.mapper");
    }

    public function cleanDatabase()
    {
        $schema = $this->container->get('db.schema');
        $schema->drop();
        $schema->create();
    }

    /**
     * @Transform :start
     * @Transform :end
     */
    public function transformStringToDate($string)
    {
        return new DateTime($string);
    }

    /**
     * @Transform :startString
     * @Transform :endString
     */
    public function transformStringToRfc3339DateString($string)
    {
        return (new DateTime($string))->format(DATE_RFC3339);
    }

    /**
     * @Transform :count
     */
    public function transformStringToIngeger($string)
    {
        return (int) $string;
    }

    /**
     * @Given I am an employee
     */
    public function iAmAnEmployee()
    {
        $this->employee = User::employeeNamedWithEmail("Richard Roma", "ricky@roma.com");
        $this->userMapper->insert($this->employee);

        $this->restApiBrowser->setRequestHeader("x-access-token", "i_am_an_employee");
    }

    /**
     * @Given I am a manager
     */
    public function iAmAManager()
    {
        $this->manager = User::managerNamedWithEmail("John Williamson", "jwilliamson@gmail.com");
        $this->userMapper->insert($this->manager);

        $this->restApiBrowser->setRequestHeader("x-access-token", "i_am_a_manager");
    }
}
