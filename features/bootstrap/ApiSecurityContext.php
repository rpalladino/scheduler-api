<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Rezzza\RestApiBehatExtension\Rest\RestApiBrowser;
use Rezzza\RestApiBehatExtension\Json\JsonInspector;

/**
 * Defines application features from the specific context.
 */
class ApiSecurityContext implements Context, SnippetAcceptingContext
{
    private $restApiBrowser;
    private $jsonInspector;

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
    }

    /**
     * @Given I provide a valid access token
     */
    public function iProvideAValidAccessToken()
    {
        $this->restApiBrowser->setRequestHeader("x-access-token", "i_am_a_manager");
    }

    /**
     * @Given I provide a valid access token for an unknown user
     */
    public function iProvideAValidAccessTokenForAnUnknownUser()
    {
        $this->restApiBrowser->setRequestHeader("x-access-token", "i_am_an_employee");
    }

    /**
     * @Given I provide an invalid access token
     */
    public function iProvideAnInvalidAccessToken()
    {
        $this->restApiBrowser->setRequestHeader("x-access-token", "invalid-token");
    }

    /**
     * @When I access the api
     */
    public function iAccessTheApi()
    {
       $this->restApiBrowser->sendRequest('GET', "/");
    }

    /**
     * @Then I should see an unauthenticated message
     */
    public function iShouldSeeAnUnauthenticatedMessage()
    {
        expect($this->restApiBrowser->getResponse()->getStatusCode())->toBe(401);
        expect($this->jsonInspector->readJsonNodeValue("status"))->toBe(401);
        expect($this->jsonInspector->readJsonNodeValue("title", "Unauthenticated"));
    }

    /**
     * @Then I should see a success message
     */
    public function iShouldSeeASuccessMessage()
    {
        expect($this->restApiBrowser->getResponse()->getStatusCode())->toBe(200);
    }
}
