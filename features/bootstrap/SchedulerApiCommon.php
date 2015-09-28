<?php

use Aura\Di\ContainerBuilder;
use Dotenv\Dotenv;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\User\User;
use Scheduler\Web\Radar\Config\ServiceConfig;

trait SchedulerApiCommon
{
    private $restApiBrowser;
    private $jsonInspector;

    private $container;

    private $shiftMapper;
    private $userMapper;

    private $employee;
    private $manager;

    private $shifts = [];

    private $accessToken;

    public function initialize($restApiBrowser, $jsonInspector)
    {
        $this->restApiBrowser = $restApiBrowser;
        $this->jsonInspector = $jsonInspector;

        $dotenvPath = __DIR__ . "/../..";
        if (file_exists($dotenvPath . "/.env")) {
            $dotenv = new Dotenv($dotenvPath);
            $dotenv->load();
        }

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
     * @Transform :date
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
     * @Given there is a manager
     */
    public function thereIsAManager()
    {
        $this->manager = User::managerNamedWithEmail("John Williamson", "jwilliamson@gmail.com");
        $this->userMapper->insert($this->manager);
    }

    /**
     * @Given there is an employee
     */
    public function thereIsAnEmployee()
    {
        $this->employee = User::employeeNamedWithEmail("Richard Roma", "ricky@roma.com");
        $this->userMapper->insert($this->employee);
    }

    /**
     * @Given there is an employee named :name
     */
    public function thereIsAnEmployeeNamed($name)
    {
        $this->employee = User::employeeNamedWithEmail($name, "employee@example.com");
        $this->userMapper->insert($this->employee);
    }

    /**
     * @Given I am an employee
     */
    public function iAmAnEmployee()
    {
        $this->accessToken = "i_am_an_employee";
    }

    /**
     * @Given I am a manager
     */
    public function iAmAManager()
    {
        $this->accessToken = "i_am_a_manager";
    }

    /**
     * @Then /^there (?:are|should be) (\d+) shifts? in the schedule$/i
     */
    public function thereShouldBeShiftsInTheSchedule($count)
    {
        expect($this->shifts)->toHaveCount((int) $count);
    }
}
