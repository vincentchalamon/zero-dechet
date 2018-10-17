Feature: CRUD Team
  In order to use the Team API
  As a user or an admin
  I need to be able to retrieve, create, update and delete Team resources.

  Scenario Outline: As any user, I can get a list of teams
    Given the following user:
      | email                 | roles           | active |
      | admin@example.com     | ROLE_ADMIN      | true   |
      | adminCity@example.com | ROLE_ADMIN_CITY | true   |
      | foo@example.com       | ROLE_USER       | true   |
      | bar@example.com       | ROLE_USER       | true   |
    And I am authenticated as "<email>"
    And the following teams:
      | name | users                            |
      | Bar  | foo@example.com, bar@example.com |
      | Foo  | bar@example.com                  |
    When I get a list of teams
    Then I see a list of teams
    And the JSON node "hydra:totalItems" should be equal to 2
    Examples:
      | email                 |
      | admin@example.com     |
      | adminCity@example.com |
      | foo@example.com       |
      | bar@example.com       |

  Scenario: As an admin, I can create a team
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
      | bar@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    When I create a team with the following users:
      | foo@example.com |
      | bar@example.com |
    Then I see a team

  Scenario: As a city admin, I cannot create a team
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    When I create a team
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot create a team
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I create a team
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot create a team
    When I create a team
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can update a team
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
      | bar@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And there is a team
    When I update a team with the following users:
      | foo@example.com |
      | bar@example.com |
    Then I see a team

  Scenario: As a city admin, I cannot update a team
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And there is a team
    When I update a team
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot update a team
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there is a team
    When I update a team
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot update a team
    Given there is a team
    When I update a team
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can delete a team
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there is a team
    When I delete a team
    Then the team has been successfully deleted

  Scenario: As a city admin, I cannot delete a team
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And there is a team
    When I delete a team
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot delete a team
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there is a team
    When I delete a team
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot delete a team
    Given there is a team
    When I delete a team
    Then I am unauthorized to access this resource
