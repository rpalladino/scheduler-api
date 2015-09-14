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

        if ($this->restApiBrowser->getResponse()->getStatusCode() == 200) {
            $this->shifts =  $this->jsonInspector->readJsonNodeValue('shifts');
        }
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
