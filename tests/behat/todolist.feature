@mod @mod_booking @bookingextension_todolist
Feature: Todo list booking extension
  In order to track option preparation tasks
  As a booking manager and teacher
  I need to configure and use the todo list extension end to end

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | One      | manager1@example.com |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | manager1 | C1     | editingteacher |
      | teacher1 | C1     | teacher        |
      | student1 | C1     | student        |
    And I clean booking cache
    And the following "activities" exist:
      | activity | course | name      | intro        | bookingmanager |
      | booking  | C1     | MyBooking | Booking intro | manager1      |

  Scenario: Manager sees todo list fields in option edit form
    Given the following "mod_booking > options" exist:
      | booking   | text    | course | description | optiondateid_0 | coursestarttime_0 | courseendtime_0 |
      | MyBooking | OptionA | C1     | Desc A      | 0              | 2346937200        | 2347110000      |
    And I am on the "MyBooking" Activity page logged in as manager1
    When I click on "Edit booking option" "icon" in the ".allbookingoptionstable_r1" "css_element"
    Then I should see "Enable todo list"
    And I should see "Todo list"

  Scenario: Manager can save todo items and see them in option view
    Given the following "mod_booking > options" exist:
      | booking   | text    | course | description | optiondateid_0 | coursestarttime_0 | courseendtime_0 |
      | MyBooking | OptionA | C1     | Desc A      | 0              | 2346937200        | 2347110000      |
    And I am on the "MyBooking" Activity page logged in as manager1
    When I click on "Edit booking option" "icon" in the ".allbookingoptionstable_r1" "css_element"
    And I set the field "Enable todo list" to "1"
    And I set the field "Todo list" to "Prepare room\nBring materials"
    And I press "Save"
    Then I should see "Prepare room"
    And I should see "Bring materials"

  Scenario: Empty enabled todo list renders empty placeholder
    Given the following "mod_booking > options" exist:
      | booking   | text    | course | description | optiondateid_0 | coursestarttime_0 | courseendtime_0 |
      | MyBooking | OptionA | C1     | Desc A      | 0              | 2346937200        | 2347110000      |
    And I am on the "MyBooking" Activity page logged in as manager1
    When I click on "Edit booking option" "icon" in the ".allbookingoptionstable_r1" "css_element"
    And I set the field "Enable todo list" to "1"
    And I set the field "Todo list" to ""
    And I press "Save"
    Then I should see "No todo items configured."

  @javascript
  Scenario: Teacher can check an item
    Given the following "mod_booking > options" exist:
      | booking   | text    | course | description | optiondateid_0 | coursestarttime_0 | courseendtime_0 | enable_todolist | todolist_items                  |
      | MyBooking | OptionA | C1     | Desc A      | 0              | 2346937200        | 2347110000      | 1               | Prepare room\nBring materials |
    And I am on the "MyBooking" Activity page logged in as teacher1
    When I click on ".bookingextension-todolist input[data-action='toggle-todolist-item']" "css_element"
    Then I should see "Todo item marked as done."

  @javascript
  Scenario: Teacher can uncheck an item
    Given the following "mod_booking > options" exist:
      | booking   | text    | course | description | optiondateid_0 | coursestarttime_0 | courseendtime_0 | enable_todolist | todolist_items |
      | MyBooking | OptionA | C1     | Desc A      | 0              | 2346937200        | 2347110000      | 1               | One task       |
    And I am on the "MyBooking" Activity page logged in as teacher1
    And I click on ".bookingextension-todolist input[data-action='toggle-todolist-item']" "css_element"
    When I click on ".bookingextension-todolist input[data-action='toggle-todolist-item']" "css_element"
    Then I should see "Todo item marked as not done."

  @javascript
  Scenario: Completing last item shows completion notification
    Given the following "mod_booking > options" exist:
      | booking   | text    | course | description | optiondateid_0 | coursestarttime_0 | courseendtime_0 | enable_todolist | todolist_items      |
      | MyBooking | OptionA | C1     | Desc A      | 0              | 2346937200        | 2347110000      | 1               | Task one\nTask two |
    And I am on the "MyBooking" Activity page logged in as teacher1
    And I click on ".bookingextension-todolist input[data-action='toggle-todolist-item']:nth-of-type(1)" "css_element"
    When I click on ".bookingextension-todolist input[data-action='toggle-todolist-item']:nth-of-type(2)" "css_element"
    Then I should see "Congratulations! You have completed all todo items."

  Scenario: Student sees todo list items
    Given the following "mod_booking > options" exist:
      | booking   | text    | course | description | optiondateid_0 | coursestarttime_0 | courseendtime_0 | enable_todolist | todolist_items |
      | MyBooking | OptionA | C1     | Desc A      | 0              | 2346937200        | 2347110000      | 1               | One task       |
    And I am on the "MyBooking" Activity page logged in as student1
    Then I should see "One task"

  Scenario: Booking history includes toggle entry
    Given the following "mod_booking > options" exist:
      | booking   | text    | course | description | optiondateid_0 | coursestarttime_0 | courseendtime_0 | enable_todolist | todolist_items |
      | MyBooking | OptionA | C1     | Desc A      | 0              | 2346937200        | 2347110000      | 1               | One task       |
    And I am on the "MyBooking" Activity page logged in as teacher1
    And I click on ".bookingextension-todolist input[data-action='toggle-todolist-item']" "css_element"
    When I click on "Settings" "icon" in the ".allbookingoptionstable_r1" "css_element"
    And I click on "Bookings tracker" "link" in the ".allbookingoptionstable_r1" "css_element"
    And I click on "Booking history" "text" in the "#accordion-heading-bookinghistory" "css_element"
    Then I should see "todo list item"

  Scenario: Booking history includes completed entry
    Given the following "mod_booking > options" exist:
      | booking   | text    | course | description | optiondateid_0 | coursestarttime_0 | courseendtime_0 | enable_todolist | todolist_items      |
      | MyBooking | OptionA | C1     | Desc A      | 0              | 2346937200        | 2347110000      | 1               | Task one\nTask two |
    And I am on the "MyBooking" Activity page logged in as teacher1
    And I click on ".bookingextension-todolist input[data-action='toggle-todolist-item']:nth-of-type(1)" "css_element"
    And I click on ".bookingextension-todolist input[data-action='toggle-todolist-item']:nth-of-type(2)" "css_element"
    When I click on "Settings" "icon" in the ".allbookingoptionstable_r1" "css_element"
    And I click on "Bookings tracker" "link" in the ".allbookingoptionstable_r1" "css_element"
    And I click on "Booking history" "text" in the "#accordion-heading-bookinghistory" "css_element"
    Then I should see "Todo list for option"

  Scenario: Rule type for not completed todo list is available
    Given I am on the "MyBooking" Activity page logged in as manager1
    When I navigate to "Booking rules" in current page administration
    And I press "Add booking rule"
    Then I should see "Before a date (only with incomplete todo list)"
