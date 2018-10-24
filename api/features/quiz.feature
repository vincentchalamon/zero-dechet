Feature: CRUD Quiz
  In order to use the Quiz API
  As a user or an admin
  I need to be able to retrieve, create, update and delete Quiz resources.

  Scenario: As an admin, I can get a list of quizzes
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there are valid quizzes
    When I get a list of quizzes
    Then I see a list of quizzes

  Scenario: As a user, I can get a list of quizzes
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there are valid quizzes
    When I get a list of quizzes
    Then I see a list of quizzes

  Scenario: As anonymous, I cannot get a list of quizzes
    Given there are valid quizzes
    When I get a list of quizzes
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can get a quiz
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there are valid quizzes
    When I get a quiz
    Then I see a quiz

  Scenario: As a user, I can get a quiz
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there are valid quizzes
    When I get a quiz
    Then I see a quiz

  Scenario: As anonymous, I cannot get a quiz
    Given there are valid quizzes
    When I get a quiz
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can create a quiz
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there is a place
    When I create a new quiz
    Then I see a quiz

  Scenario: As a user, I cannot create a quiz
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I create a quiz
    Then I am forbidden to access this resource

  Scenario: As a city, I cannot create a quiz
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    When I create a quiz
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot create a quiz
    When I create a quiz
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can update a quiz
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there are valid quizzes
    When I update an existing quiz
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be valid according to this schema:
    """
    {
      "type": "object",
      "properties": {
        "@id": {"pattern": "^/quizzes/[\\w-]+$"},
        "@type": {"pattern": "^Quiz$"},
        "@context": {"pattern": "^/contexts/Quiz$"},
        "place": {
          "type": "object",
          "properties": {
            "@id": {"pattern": "^/places/[\\w-]+$"},
            "@type": {"pattern": "^Place$"}
          }
        },
        "questions": {
          "type": ["array", "object"],
          "items": {
            "type": "object",
            "properties": {
              "@id": {"pattern": "^/questions/[\\w-]+$"},
              "@type": {"pattern": "^Question$"},
              "title": {"type": "string"},
              "choices": {
                "type": "array",
                "items": {
                  "type": "object",
                  "properties": {
                    "@id": {"pattern": "^/choices/[\\w-]+$"},
                    "@type": {"pattern": "^Choice$"},
                    "name": {"type": "string"}
                  }
                }
              }
            }
          }
        }
      }
    }
    """

  Scenario: As an admin, I cannot update a quiz which has results
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And there are valid quizzes
    And user "foo@example.com" has quizzes
    When I update an existing quiz
    Then I am forbidden to access this resource

  Scenario: As a city admin, I cannot update a quiz
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And there are valid quizzes
    When I update an existing quiz
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot update a quiz
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there are valid quizzes
    When I update an existing quiz
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot update a quiz
    Given there are valid quizzes
    When I update an existing quiz
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can delete a quiz
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there are valid quizzes
    When I delete a quiz
    Then the quiz has been successfully deleted

  Scenario: As an admin, I cannot delete a quiz which has results
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And there are valid quizzes
    And user "foo@example.com" has quizzes
    When I delete a quiz
    Then I am forbidden to access this resource

  Scenario: As a city admin, I cannot delete a quiz
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And there are valid quizzes
    When I delete a quiz
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot delete a quiz
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there are valid quizzes
    When I delete a quiz
    Then I am forbidden to access this resource

  Scenario: As anonymous, I cannot delete a quiz
    And there are valid quizzes
    When I delete a quiz
    Then I am unauthorized to access this resource
