Feature: CRUD Tag
  In order to use the Tag API
  As a user or an admin
  I need to be able to retrieve, create, update and delete Tag resources.

  Scenario: As an admin, I can get a list of tags
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there are 3 tags
    When I get a list of tags
    Then I see a list of tags

  Scenario: As a user, I can get a list of tags
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there are 3 tags
    When I get a list of tags
    Then I see a list of tags

  Scenario: As an admin, I can create a tag
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    When I create a tag
    Then I see a tag

  Scenario: As anonymous, I cannot create a tag
    When I create a tag
    Then I am unauthorized to access this resource

  Scenario: As a user, I cannot create a tag
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I create a tag
    Then I am forbidden to access this resource

  Scenario: As an admin, I can update a tag
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there is a tag
    When I update a tag
    Then I see a tag

  Scenario: As a user, I cannot update a tag
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there is a tag
    When I update a tag
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot update a tag
    Given there is a tag
    When I update a tag
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can delete a tag
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there is a tag
    When I delete a tag
    Then the tag has been successfully deleted

  Scenario: As a user, I cannot delete a tag
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there is a tag
    When I delete a tag
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot delete a tag
    Given there is a tag
    When I delete a tag
    Then I am unauthorized to access this resource
