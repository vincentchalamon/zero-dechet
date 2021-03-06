Feature: CRUD User
  In order to use the User API
  As a user or an admin
  I need to be able to retrieve, create, update and delete User resources.

  Scenario: As an admin, I can access a user
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    When I get user "foo@example.com"
    Then I see a user
    And the JSON node roles should exist

  Scenario: As a user, I cannot access another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I get user "bar@example.com"
    Then I am forbidden to access this resource

  Scenario: As a user, I can access my own account
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I get user "foo@example.com"
    Then I see a user

  Scenario: As an admin, I can update a user
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    When I update user "foo@example.com"
    Then I see a user

  Scenario: As a user, I can update my own account
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I update user "foo@example.com"
    Then I see a user

  Scenario: As a user, I cannot update another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I update user "bar@example.com"
    Then I am forbidden to access this resource

  Scenario: As an admin, I can delete a user
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    When I delete user "foo@example.com"
    Then the user "foo@example.com" has been successfully deleted

  Scenario: As a user, I cannot delete another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I delete user "bar@example.com"
    Then I am forbidden to access this resource

  Scenario: As a user, I can delete my own account
    Given the following user:
      | email           | roles     | active | plainPassword |
      | foo@example.com | ROLE_USER | true   | p4ssw0rd      |
    And I am authenticated as "foo@example.com"
    When I delete user "foo@example.com"
    Then the user "foo@example.com" has been successfully deleted
    And I log in with "foo@example.com" p4ssw0rd
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can access a list of users
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there are 3 users
    When I get a list of users
    Then I see a list of users

  Scenario: As a user, I cannot access a list of users
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there are 3 users
    When I get a list of users
    Then I am forbidden to access this resource

  Scenario: As anonymous, I can register
    When I register
    Then I see a user
    And I receive an email to validate my registration
    And user has been successfully created

  Scenario: As anonymous, I can validate my registration
    Given the following user:
      | active | token |
      | false  | foo   |
    When I validate my account
    Then I see a user
    And user has been validated

  Scenario: As anonymous, I can't validate my registration using an invalid token
    Given the following user:
      | active | token |
      | false  | foo   |
    When I validate my account with token bar
    Then I am unauthorized to access this resource
    And user has not been validated

  Scenario: As an admin, I can create a user
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    When I register a user
    Then I see a user
    And user has been successfully created

  Scenario: As anonymous, I cannot register using an existing email address
    Given the following user:
      | email                |
      | JohN.Doe@eXample.coM |
    When I register
    Then the request is invalid

  Scenario: As anonymous, I cannot register without accepting CGU
    Given the following user:
      | email                |
      | JohN.Doe@eXample.coM |
    When I register
    Then the request is invalid

  Scenario Outline: As anonymous, I cannot register with an invalid password
    When I register with password "<password>"
    Then the request is invalid
    And the JSON node "hydra:description" should contain "<message>"
    Examples:
      | password | message                                                                               |
      |          | plainPassword: Cette valeur ne doit pas être vide.                                    |
      | bar      | plainPassword: Cette chaîne est trop courte. Elle doit avoir au minimum 7 caractères. |

  Scenario: As a user, I can update my password
    Given the following user:
      | email           | plainPassword | roles     | active |
      | foo@example.com | p4ssw0rd      | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I update my password
    Then I see a user

  Scenario Outline: As a user, I cannot update my password with an invalid current password
    Given the following user:
      | email           | plainPassword | roles     | active |
      | foo@example.com | p4ssw0rd      | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I update my password with current password equal to "<currentPassword>"
    Then the request is invalid
    And the JSON node "hydra:description" should contain "currentPassword: Le mot de passe est invalide."
    Examples:
      | currentPassword |
      |                 |
      | foo             |

  # todo As a user, I cannot add an invalid choice on a UserQuiz
  Scenario: As an admin, I can access a user quizzes
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And there are valid quizzes
    And user "foo@example.com" has quizzes
    And I am authenticated as "admin@example.com"
    When I get user "foo@example.com" quizzes
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be valid according to this schema:
    """
    {
      "type": "object",
      "properties": {
        "@id": {
          "type": "string",
          "pattern": "^\/users\/[\\w-]+\/quizzes$"
        },
        "@type": {
          "type": "string",
          "pattern": "^hydra:Collection$"
        },
        "hydra:member": {
          "type": "array",
          "minItems": 3,
          "maxItems": 3,
          "items": {
            "type": "object",
            "properties": {
              "@id": {
                "type": "string",
                "pattern": "^\/user_quizzes\/[\\w-]+$"
              },
              "@type": {
                "type": "string",
                "pattern": "^UserQuiz$"
              },
              "choices": {
                "type": "array",
                "items": {
                  "type": "object",
                  "properties": []
                }
              },
              "@context": {
                "type": "string",
                "pattern": "^\/contexts\/UserQuiz$"
              }
            }
          }
        },
        "hydra:totalItems": {
          "type": "integer"
        }
      }
    }
    """

  Scenario: As an admin, I cannot access a non existing user quizzes
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And I add "Accept" header equal to "application/ld+json"
    When I send a GET request to "/users/12345/quizzes"
    Then the user is not found

  Scenario: As a user, I cannot access another user quizzes
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And there are valid quizzes
    And user "bar@example.com" has quizzes
    And I am authenticated as "foo@example.com"
    When I get user "bar@example.com" quizzes
    Then I am forbidden to access this resource

  Scenario: As a user, I can create a UserQuiz
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And there are valid quizzes
    And I am authenticated as "foo@example.com"
    When I create a new userQuiz
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON should be valid according to this schema:
    """
    {
      "type": "object",
      "properties": {
        "@id": {"pattern": "^/user_quizzes/[\\w-]+$"},
        "@type": {"pattern": "^UserQuiz$"},
        "@context": {"pattern": "^/contexts/UserQuiz$"},
        "choices": {
          "type": "array",
          "items": {
            "type": "object",
            "properties": {
              "question": {
                "type": "object",
                "properties": {
                  "title": {"type": "string"}
                }
              },
              "name": {"type": "string"}
            }
          }
        }
      }
    }
    """

  Scenario: As a user, I cannot create a UserQuiz for another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And there are valid quizzes
    And I am authenticated as "foo@example.com"
    When I create a new userQuiz with user "bar@example.com"
    Then the response status code should be 201
    And the userQuiz should be attached to "foo@example.com"

  Scenario: As an admin, I can get user scores
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And there are valid quizzes
    And user "foo@example.com" has quizzes
    And I am authenticated as "admin@example.com"
    When I get user "foo@example.com" scores
    Then I see user scores
