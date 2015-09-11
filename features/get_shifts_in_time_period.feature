Feature: Get shifts within a specific time period
  In order to see the schedule
  As a manager
  I need to list shifts within a specific time period

  Scenario: List shifts in a single day period
    Given there is a shift starting at "2015-09-03 05:00 AM" and ending at "2015-09-03 12:00 PM"
     And there is a shift starting at "2015-09-03 10:00 AM" and ending at "2015-09-03 03:00 PM"
     And there is a shift starting at "2015-09-03 02:00 PM" and ending at "2015-09-03 07:00 PM"
    When I list the shifts between '2015-09-03 12:00 AM' and '2015-09-03 11:59 PM'
    Then there should be 3 shifts in the schedule

  Scenario: List shifts for a multiple-day period
    Given there is a shift starting at "2015-08-24 09:00 AM" and ending at "2015-08-24 01:00 PM"
     And there is a shift starting at "2015-08-24 01:00 PM" and ending at "2015-08-24 09:00 PM"
     And there is a shift starting at "2015-08-25 09:00 AM" and ending at "2015-08-25 01:00 PM"
     And there is a shift starting at "2015-08-25 01:00 PM" and ending at "2015-08-25 09:00 PM"
    When I list the shifts between "2015-08-24 09:00 AM" and "2015-08-31 05:00 PM"
    Then there should be 4 shifts in the schedule

  Scenario: List shifts in a period of hours on a single day
    Given there is a shift starting at "2015-09-04 05:00 AM" and ending at "2015-09-04 12:00 PM"
    And there is a shift starting at "2015-09-04 11:00 AM" and ending at "2015-09-04 06:00 PM"
    When I list the shifts between '2015-09-04 05:00 AM' and '2015-09-04 10:00 AM'
    Then there should be 1 shift in the schedule

  Scenario: No shifts found in time period
    Given there is a shift starting at "2015-09-04 05:00 AM" and ending at "2015-09-04 12:00 PM"
    When I list the shifts between '2015-09-05 09:00 AM' and '2015-09-05 09:00 PM'
    Then there should be 0 shifts in the schedule
