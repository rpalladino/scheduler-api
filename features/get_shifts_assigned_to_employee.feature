Feature: Get shifts assigned to employee
  In order to know when I am working
  As an employee
  I need to see all of the shifts assigned to me

  Scenario: Get shifts assigned to specific employee
    Given there is a shift assigned to "me" starting at "2015-09-03 05:00 AM" and ending at "2015-09-03 12:00 PM"
     And there is a shift assigned to "me" starting at "2015-09-03 10:00 AM" and ending at "2015-09-03 03:00 PM"
     And there is a shift assigned to "Shelly Levene" starting at "2015-09-03 02:00 PM" and ending at "2015-09-03 07:00 PM"
    When I list the shifts assigned to me
    Then there should be 2 shifts in the schedule

  Scenario: Get shifts assigned to employee including open shifts (not assigned to any one)
