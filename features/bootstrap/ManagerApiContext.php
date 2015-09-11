<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Aura\Di\ContainerBuilder;
use Dotenv\Dotenv;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;
use Scheduler\Domain\Model\User\UserMapper;
use Scheduler\REST\Radar\Config\ServiceConfig;

/**
 * Defines application features from the specific context.
 */
class ManagerApiContext implements Context, SnippetAcceptingContext
{
    private $shiftMapper;
    private $userMapper;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $dotenv = new Dotenv(__DIR__ . "/../../");
        $dotenv->load();

        $builder = new ContainerBuilder();
        $this->container = $builder->newConfiguredInstance([ServiceConfig::class]);

        $this->shiftMapper = $this->container->get(ShiftMapper::class);
        $this->userMapper = $this->container->get(UserMapper::class);
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
     * @BeforeScenario
     */
    public function cleanDatabase()
    {
        $schema = $this->container->get('db.schema');
        $schema->drop();
        $schema->create();
    }

    /**
     * @BeforeScenario
     */
    public function setManager()
    {
        $this->manager = User::managerNamedWithEmail("John Williamson", "jwilliamson@gmail.com");
        $this->userMapper->insert($this->manager);
    }

    /**
     * @Given there is a shift starting at :start and ending at :end
     */
    public function thereIsAShiftStartingAtAndEndingAt($start, $end)
    {
        $aShift = Shift::withManagerAndTimes($this->manager, $start, $end);
        $this->shiftMapper->insert($aShift);
    }

    /**
     * @When I list the shifts between :arg1 and :arg2
     */
    public function iListTheShiftsBetweenAnd($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then there should be :arg1 shifts in the schedule
     */
    public function thereShouldBeShiftsInTheSchedule($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then there should be :arg1 shift in the schedule
     */
    public function thereShouldBeShiftInTheSchedule($arg1)
    {
        throw new PendingException();
    }
}
