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

    protected static $shiftCount;

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
        static::$shiftCount = 0;
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
    public function asAnEmployee()
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
     * @Given there is a manager named :name with the email :email and phone :phone
     */
    // public function thereIsAManagerNamedWithTheEmailAndPhone($name, $email, $phone)
    // {
    //     $aManager = new User(null, $name, "manager", $email, $phone);
    //     $this->userMapper->insert($aManager);
    // }

    /**
     * @Given I was assigned :count shift(s) by the manager named :name with the email :email and phone :phone
     */
    public function iWasAssignedShiftByTheManagerNamedWithTheEmailAndPhone($count, $name, $email, $phone)
    {
        $theManager = new User(null, $name, "manager", $email, $phone);
        $this->userMapper->insert($theManager);

        foreach (range(1, $count) as $i) {
            self::$shiftCount += 1;
            $shiftCount = self::$shiftCount;

            $start = new DateTime("today +$shiftCount day 10:30am");
            $end =  new DateTime("today +$shiftCount day 3:00pm");
            $aShift = new Shift(null, $theManager, $this->employee, 0.5, $start, $end);

            $this->shiftMapper->insert($aShift);
        }
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

    /**
     * @Then I should see :count shift(s) where the manager is named :name
     */
    public function iShouldSeeShiftWhereTheManagerIsNamed($count, $name)
    {
        $shifts = array_filter($this->shifts, function ($shift) use ($name) {
            return $shift->manager->name == $name;
        });
        expect(count($shifts))->toBe($count);
    }

    /**
     * @Then I should see :count shift(s) where the manager has the email :email
     */
    public function iShouldSeeShiftWhereTheManagerHasTheEmail($count, $email)
    {
        $shifts = array_filter($this->shifts, function ($shift) use ($email) {
            return $shift->manager->email == $email;
        });
        expect(count($shifts))->toBe($count);
    }

    /**
     * @Then I should see :count shift(s) where the manager has the phone :phone
     */
    public function iShouldSeeShiftWhereTheManagerHasThePhone($count, $phone)
    {
        $shifts = array_filter($this->shifts, function ($shift) use ($phone) {
            return $shift->manager->phone == $phone;
        });
        expect(count($shifts))->toBe($count);
    }
}
