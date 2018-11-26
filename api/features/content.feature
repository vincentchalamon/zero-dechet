Feature: CRUD Content
  In order to use the Content API
  As an admin
  I need to be able to retrieve, create, update and delete Content resources.

  Scenario: As an admin, I can get a list of contents
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    When I get a list of contents
    Then I see a list of contents

  Scenario: As a user, I can get a list of contents
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following contents:
      | published |
      | true      |
      | false     |
      | true      |
    When I get a list of contents
    Then I see a list of contents
    And the JSON node "hydra:totalItems" should be equal to 2

  Scenario: As anonymous, I cannot get a list of contents
    Given the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    When I get a list of contents
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can get a published content
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following content:
      | published |
      | true      |
    When I get a content
    Then I see a content

  Scenario: As a user, I can get a published content
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following content:
      | published |
      | true      |
    When I get a content
    Then I see a content

  Scenario: As anonymous, I cannot get a published content
    Given the following content:
      | published |
      | true      |
    When I get a content
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can get a non-published content
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following content:
      | published |
      | false     |
    When I get a content
    Then I see a content

  Scenario: As a user, I cannot get a non-published content
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following content:
      | published |
      | false     |
    When I get a content
    Then the content is not found

  Scenario: As anonymous, I cannot get a non-published content
    Given the following content:
      | published |
      | false     |
    When I get a content
    Then the content is not found

  Scenario: As an admin, I can create a content
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    When I create a content
    Then I see a content

  Scenario: As a user, I cannot create a content
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I create a content
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot create a content
    When I create a content
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can update a content
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there is a content
    When I update a content
    Then I see a content

  Scenario: As a user, I cannot update a content
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following content:
      | published |
      | true      |
    When I update a content
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot update a content
    Given the following content:
      | published |
      | true      |
    When I update a content
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can delete a content
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following content:
      | published |
      | true      |
    When I delete a content
    Then the content has been successfully deleted

  Scenario: As a user, I cannot delete a content
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following content:
      | published |
      | true      |
    When I delete a content
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot delete a content
    Given the following content:
      | published |
      | true      |
    When I delete a content
    Then I am unauthorized to access this resource
