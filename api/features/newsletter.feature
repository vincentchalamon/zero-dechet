Feature: CRUD Newsletter
  In order to use the Newsletter API
  As a user or an admin
  I need to be able to retrieve, create, update and delete Newsletter resources.

  Scenario: As an admin, I can get a list of newsletters
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following newsletter:
      | status  |
      | pending |
      | sending |
      | sent    |
    When I get a list of newsletters
    Then I see a list of newsletters
    And the JSON node "hydra:totalItems" should be equal to 3

  Scenario: As a city admin, I can get a list of newsletters sent
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And the following newsletter:
      | status  |
      | pending |
      | sending |
      | sent    |
    When I get a list of newsletters
    Then I see a list of newsletters
    And the JSON node "hydra:totalItems" should be equal to 1

  Scenario: As a user, I cannot get a list of newsletters sent
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following newsletters:
      | status  |
      | pending |
      | sending |
      | sent    |
    When I get a list of newsletters
    Then I see a list of newsletters
    And the JSON node "hydra:totalItems" should be equal to 1

  Scenario Outline: As an admin, I can get a newsletter
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following newsletter:
      | status   |
      | <status> |
    When I get a newsletter
    Then I see a newsletter
    Examples:
      | status  |
      | pending |
      | sending |
      | sent    |

  Scenario Outline: As a city admin or a user, I cannot get a newsletter which is not sent yet
    Given the following user:
      | email            | roles   | active |
      | user@example.com | <roles> | true   |
    And I am authenticated as "user@example.com"
    And the following newsletter:
      | status   |
      | <status> |
    When I get a newsletter
    Then the newsletter is not found
    Examples:
      | roles           | status  |
      | ROLE_USER       | pending |
      | ROLE_USER       | sending |
      | ROLE_ADMIN_CITY | pending |
      | ROLE_ADMIN_CITY | sending |

  Scenario Outline: As a city admin or a user, I can get a newsletter sent
    Given the following user:
      | email            | roles   | active |
      | user@example.com | <roles> | true   |
    And I am authenticated as "user@example.com"
    And the following newsletter:
      | status |
      | sent   |
    When I get a newsletter
    Then I see a newsletter
    Examples:
      | roles           |
      | ROLE_USER       |
      | ROLE_ADMIN_CITY |

  Scenario: As an admin, I can create a newsletter
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    When I create a newsletter with:
      | status  |
      | pending |
    Then I see a newsletter

  Scenario: As a city admin, I cannot create a newsletter
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    When I create a newsletter with:
      | status  |
      | pending |
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot create a newsletter
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I create a newsletter with:
      | status  |
      | pending |
    Then I am forbidden to access this resource

  Scenario: As an admin, I can update a newsletter
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following newsletter:
      | status  |
      | pending |
    When I update a newsletter with:
      | status  |
      | pending |
    Then I see a newsletter
    And 0 mail should be sent

  Scenario: As an admin, I can send a newsletter
    Given the following users:
      | newsletter | email             | roles      | active |
      | true       | admin@example.com | ROLE_ADMIN | true   |
      | true       | foo@example.com   | ROLE_USER  | true   |
      | true       | bar@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following newsletter:
      | status  |
      | pending |
    When I update a newsletter with:
      | status  |
      | sending |
    Then I see a newsletter
    And 3 mails should be sent

  Scenario: As a city admin, I cannot update a newsletter
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And the following newsletter:
      | status |
      | sent   |
    When I update a newsletter
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot update a newsletter
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following newsletter:
      | status |
      | sent   |
    When I update a newsletter
    Then I am forbidden to access this resource

  Scenario: As an admin, I can delete a newsletter
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following newsletter:
      | status  |
      | pending |
    When I delete a newsletter
    Then the newsletter has been successfully deleted

  Scenario: As a city admin, I cannot delete a newsletter
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And the following newsletter:
      | status |
      | sent   |
    When I delete a newsletter
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot delete a newsletter
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following newsletter:
      | status |
      | sent   |
    When I delete a newsletter
    Then I am forbidden to access this resource
