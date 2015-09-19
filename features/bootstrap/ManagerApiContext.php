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
class ManagerApiContext implements Context, SnippetAcceptingContext
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
    public function asAManager()
    {
       $this->iAmAManager();
    }

    /**
     * @Given there is a(n) (open) shift starting at :start and ending at :end
     */
    public function thereIsAShiftStartingAtAndEndingAt($start, $end)
    {
        $aShift = Shift::withManagerAndTimes($this->manager, $start, $end);
        $this->shiftMapper->insert($aShift);
        $this->shifts[] = $aShift;
    }

    /**
     * @When I list the shifts between :startString and :endString
     */
    public function iListTheShiftsBetweenAnd($startString, $endString)
    {
        $url = sprintf("/shifts?start=%s&end=%s", $startString, $endString);

        $this->restApiBrowser->setRequestHeader("x-access-token", $this->accessToken);
        $this->restApiBrowser->sendRequest('GET', $url);

        if ($this->restApiBrowser->getResponse()->getStatusCode() == 200) {
            $this->shifts =  $this->jsonInspector->readJsonNodeValue('shifts');
        }
    }

    /**
     * @When I create a new shift starting at :start and ending at :end
     */
    public function iCreateANewShiftStartingAtAndEndingAt($start, $end)
    {
        $aShift = json_encode([
            "start" => $start->format(DATE_RFC2822),
            "end" => $end->format(DATE_RFC2822),
            "break" => 0.75
        ]);

        $this->restApiBrowser->setRequestHeader("content-type", "application/json");
        $this->restApiBrowser->setRequestHeader("x-access-token", $this->accessToken);
        $this->restApiBrowser->sendRequest("POST", "/shifts", $aShift);

        expect($this->restApiBrowser->getResponse()->getStatusCode())->toBe(201);
    }

    /**
     * @When I assign :employeeName to the shift
     */
    public function iAssignToTheShift($employeeName)
    {
        expect($this->employee->getName())->toBe($employeeName);
        expect($this->shifts)->toHaveCount(1);

        $theShift = array_shift($this->shifts);
        $body = json_encode([
            "employee_id" => $this->employee->getId()
        ]);

        $url = "/shifts/{$theShift->getId()}";

        $this->restApiBrowser->setRequestHeader("content-type", "application/json");
        $this->restApiBrowser->setRequestHeader("x-access-token", $this->accessToken);
        $this->restApiBrowser->sendRequest("PUT", $url, $body);

        expect($this->restApiBrowser->getResponse()->getStatusCode())->toBe(200);
    }

    /**
     * @Then the shift should be assigned to :employeeName
     */
    public function theShiftShouldBeAssignedTo($employeeName)
    {
        $shift = $this->jsonInspector->readJsonNodeValue("shift");
        expect($shift->employee->name)->toBe($employeeName);
    }

    /**
     * @Then I should not be allowed
     */
    public function iShouldNotBeAllowed()
    {
        expect($this->restApiBrowser->getResponse()->getStatusCode())->toBe(403);
        expect($this->jsonInspector->readJsonNodeValue("status"))->toBe(403);
        expect($this->jsonInspector->readJsonNodeValue("title", "Unauthorized"));
    }
}
