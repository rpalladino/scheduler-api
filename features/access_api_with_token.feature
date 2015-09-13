Feature: Access api with token
  In order to access the api
  As a user
  I need to provide an access token

  Scenario: Succeed when token header present, token valid, user exists
    Given I provide a valid access token
    When I access the api
    Then I should see a success message

  Scenario: Fail when token header present, but token invalid/missing
    Given I provide an invalid access token
    When I access the api
    Then I should see an unauthenticated message

  Scenario: Fail when token header missing
    When I access the api
    Then I should see an unauthenticated message
