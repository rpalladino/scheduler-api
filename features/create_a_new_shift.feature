Feature: Create a new shift
  In order to schedule my employees
  As a manager
  I need to create shifts for any employee

Scenario: Create a new shift for any employee
    Given there are 0 shifts in the schedule
    When I create a new shift starting at "5:00 PM" and ending at "10:00 PM"
    And I list the shifts between "5:00 PM" and "10:00 PM"
    Then there should be 1 shift in the schedule
