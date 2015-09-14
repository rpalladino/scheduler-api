<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Rezzza\RestApiBehatExtension\Rest\RestApiBrowser;
use Rezzza\RestApiBehatExtension\Json\JsonInspector;

use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\User\User;

/**
 * Defines application features from the specific context.
 */
class EmployeeApiContext implements Context, SnippetAcceptingContext
{
    use SchedulerApiCommon;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(RestApiBrowser $restApiBrowser, JsonInspector $jsonInspector)
    {
        $this->initialize($restApiBrowser, $jsonInspector);
    }

    /**
     * @BeforeScenario
     */
    public function setUp()
    {
        $this->cleanDatabase();
        $this->thereIsAManager();
        $this->thereIsAnEmployee();
    }

    /**
     * @BeforeScenario
     */
    function asAnEmployee()
    {
        $this->iAmAnEmployee();
    }

    /**
     * @Transform :employee
     */
    public function transformStringToEmployee($string)
    {
        if ($string === "me") {
            return $this->employee;
        }

        return User::employeeNamedWithEmail($string, "employee@abc.com");
    }

    /**
     * @Given there is a shift assigned to :employee starting at :start and ending at :end
     */
    public function thereIsAShiftAssignedToStartingAtAndEndingAt($employee, $start, $end)
    {
        $manager = new User(1, "John Williamson", "manager", "jwilliamson@gmail.com");
        $aShift = Shift::withManagerAndTimes($manager, $start, $end);
        $aShift = $aShift->assignTo($employee);
        $this->shiftMapper->insert($aShift);
    }

    /**
     * @When I list the shifts assigned to me
     */
    public function iListTheShiftsAssignedToMe()
    {
        $url = sprintf("/employee/%s/shifts", $this->employee->getId());
        $this->restApiBrowser->sendRequest('GET', $url);

        if ($this->restApiBrowser->getResponse()->getStatusCode() == 200) {
            $this->shifts =  $this->jsonInspector->readJsonNodeValue('shifts');
        } else {
            throw new Exception("Could not list shifts assigned to employee");
        }
    }
}
