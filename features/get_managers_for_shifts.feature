Feature: Get managers for shifts
  In order to contact my managers
  As an employee
  I need to see manager contact information for my shifts

  Scenario: Get manager's contact information for each of my shifts
    Given I was assigned 1 shift by the manager named "John" with the email "john@gmail.com" and phone "312-332-2211"
    And I was assigned 2 shifts by the manager named "Blake" with the email "blake@abc.com" and phone "312-221-3322"
    When I list the shifts assigned to me
    Then I should see 1 shift where the manager is named "John"
    And I should see 1 shift where the manager has the email "john@gmail.com"
    And I should see 1 shift where the manager has the phone "312-332-2211"
    And I should see 2 shifts where the manager is named "Blake"
    And I should see 2 shifts where the manager has the email "blake@abc.com"
    And I should see 2 shifts where the manager has the phone "312-221-3322"
