<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Rezzza\RestApiBehatExtension\Rest\RestApiBrowser;
use Rezzza\RestApiBehatExtension\Json\JsonInspector;

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
    private $restApiBrowser;
    private $jsonInspector;

    private $container;
    private $shiftMapper;
    private $userMapper;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(RestApiBrowser $restApiBrowser, JsonInspector $jsonInspector)
    {
        $this->restApiBrowser = $restApiBrowser;
        $this->jsonInspector = $jsonInspector;

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
     * @Transform :startString
     * @Transform :endString
     */
    public function transformStringToRfc3339DateString($string)
    {
        return (new DateTime($string))->format(DATE_RFC3339);
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
     * @When I list the shifts between :startString and :endString
     */
    public function iListTheShiftsBetweenAnd($startString, $endString)
    {
        $url = sprintf("/shifts?start=%s&end=%s", $startString, $endString);

        $this->restApiBrowser->sendRequest('GET', $url);

        expect($this->restApiBrowser->getResponse()->getStatusCode())->toBe(200);
        $this->shifts =  $this->jsonInspector->readJsonNodeValue('shifts');
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
