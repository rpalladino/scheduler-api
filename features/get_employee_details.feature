Feature: Get employee details
  In order to contact an employee
  As a manager
  I need to see employee details

  Scenario: Successfully view employee details
    Given there is an employee named "Shelly Levene" with email "oldguy@aol.com" and phone "312-332-2231"
    When I view the employee details
    Then I should see the employee name is "Shelly Levene"
    And I should see the employee email is "oldguy@aol.com"
    And I should see the employee phone is "312-332-2231"
