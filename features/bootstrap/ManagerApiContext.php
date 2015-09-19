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
     * @Given there is an employee named :name with email :email and phone :phone
     */
    public function thereIsAnEmployeeNamedWithEmailAndPhone($name, $email, $phone)
    {
        $anEmployee = new User(null, $name, "employee", $email, $phone);
        $this->userMapper->insert($anEmployee);
        $this->employee = $anEmployee;
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
     * @When I update the shift to start at :start and end at :end
     */
    public function iUpdateTheShiftToStartAtAndEndAt($start, $end)
    {
        expect($this->shifts)->toHaveCount(1);

        $theShift = array_shift($this->shifts);
        $body = json_encode([
            "manager_id" => $theShift->getManager()->getId(),
            "employee_id" => $theShift->getEmployee()->getId(),
            "start_time" => $start->format(DATE_RFC2822),
            "end_time" => $end->format(DATE_RFC2822),
            "break" => 0.75
        ]);

        $url = "/shifts/{$theShift->getId()}";

        $this->restApiBrowser->setRequestHeader("content-type", "application/json");
        $this->restApiBrowser->setRequestHeader("x-access-token", $this->accessToken);
        $this->restApiBrowser->sendRequest("PUT", $url, $body);

        expect($this->restApiBrowser->getResponse()->getStatusCode())->toBe(200);
    }

    /**
     * @When I view the employee details
     */
    public function iViewTheEmployeeDetails()
    {
        $url = "/employees/{$this->employee->getId()}";

        $this->restApiBrowser->setRequestHeader("x-access-token", $this->accessToken);
        $this->restApiBrowser->sendRequest("GET", $url);

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
     * @Then the shift should start at :start and end at :end
     */
    public function theShiftShouldStartAtAndEndAt($start, $end)
    {
        $shift = $this->jsonInspector->readJsonNodeValue("shift");
        expect($shift->start_time)->toBe($start->format(DATE_RFC2822));
        expect($shift->end_time)->toBe($end->format(DATE_RFC2822));
    }

    /**
     * @Then I should see the employee name is :name
     */
    public function iShouldSeeTheEmployeeNameIs($name)
    {
        $employee = $this->jsonInspector->readJsonNodeValue("user");
        expect($employee->name)->toBe($name);
    }

    /**
     * @Then I should see the employee email is :email
     */
    public function iShouldSeeTheEmployeeEmailIs($email)
    {
        $employee = $this->jsonInspector->readJsonNodeValue("user");
        expect($employee->email)->toBe($email);
    }

    /**
     * @Then I should see the employee phone is :phone
     */
    public function iShouldSeeTheEmployeePhoneIs($phone)
    {
        $employee = $this->jsonInspector->readJsonNodeValue("user");
        expect($employee->phone)->toBe($phone);
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
