Feature: CRUD Page
  In order to use the Page API
  As an anonymous, a user or an admin
  I need to be able to retrieve, create, update and delete Page resources.

  Scenario: As anonymous, I can get a list of pages
    Given there are 3 pages
    When I get a list of pages
    Then I see a list of pages

  Scenario: As anonymous, I can get a page
    Given there is a page
    When I get a page
    Then I see a page

  Scenario: As anonymous, I cannot create a page
    When I create a page
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can create a page
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    When I create a new page
    Then I see a page

  Scenario: As a city admin, I cannot create a page
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    When I create a page
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot create a page
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I create a page
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot update a page
    Given there is a page
    When I update a page
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can update a page
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there is a page
    When I update a page
    Then I see a page

  Scenario: As a city admin, I cannot update a page
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And there is a page
    When I update a page
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot update a page
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there is a page
    When I update a page
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot delete a page
    Given there is a page
    When I delete a page
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can delete a page
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there is a page
    When I delete a page
    Then the page has been successfully deleted

  Scenario: As a city admin, I cannot delete a page
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And there is a page
    When I delete a page
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot delete a page
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there is a page
    When I delete a page
    Then I am forbidden to access this resource
