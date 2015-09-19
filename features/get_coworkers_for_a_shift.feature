Feature: Get coworkers for a shift
  In order to know who I am working with
  As an employee
  I need to see the employees that are working with me during the same time period as me

  Scenario: Get single coworker with identical shift
    Given there is a shift assigned to "me" starting at "10:30 AM" and ending at "1:30 PM"
    And there is a shift assigned to "Shelly Levene" starting at "10:30 AM" and ending at "1:30 PM"
    When I view the coworkers for my shift
    Then I should have 1 coworker
    And I should see "Shelly Levene" as a coworker
