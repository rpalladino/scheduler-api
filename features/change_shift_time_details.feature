Feature: Change a shift's time details
  In order to change a shift
  As a manager
  I need to be able to update the time details

  Scenario: Successfully change a shift's time details
    Given there is a shift starting at "4:30 AM" and ending at "10:30 AM"
    When I update the shift to start at "5:30 AM" and end at "11:30 AM"
    Then the shift should start at "5:30 AM" and end at "11:30 AM"
