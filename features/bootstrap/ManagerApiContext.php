<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class ManagerApiContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given there is a shift starting at :arg1 and ending at :arg2
     */
    public function thereIsAShiftStartingAtAndEndingAt($arg1, $arg2)
    {
        throw new PendingException();
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
