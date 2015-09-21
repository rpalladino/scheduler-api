Feature: Get hours worked in week
  In order to know how much I worked
  As an employee
  I need to get a summary of hours worked for each week

  Scenario: Get hours worked this week
    Given there is a shift assigned to "me" starting at "2015-09-07 9:00 AM" and ending at "2015-09-07 1:00 PM"
    And there is a shift assigned to me starting at "2015-09-09, 11:30 AM" and ending at "2015-09-09 3:30 PM"
    And there is a shift assigned to me starting at "2015-09-11 10:00 AM" and ending at "2015-09-11 3:00 PM"
    And there is a shift assigned to "Shelly Levene" starting at "2015-09-11 10:00 AM" and ending at "2015-09-11 3:00 PM"
    When I view the summary of hours worked for week of "2015-09-11"
    Then the summary should show 13.0 hours worked
    And the summary should have a "start" date of "2015-09-07"
    And the summary should have an "end" date of "2015-09-13"
