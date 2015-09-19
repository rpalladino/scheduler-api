Feature: Change employee assigned to shift
  In order to assign a shift
  As a manager
  I need to be able to change the employee that will work a shift

  Scenario: Successfully assign employee to open shift
    Given there is an open shift starting at "4:30 AM" and ending at "10:00 AM"
    And there is an employee named "Shelly Levene"
    When I assign "Shelly Levene" to the shift
    Then the shift should be assigned to "Shelly Levene"
