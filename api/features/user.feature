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

  Scenario: As an admin, I cannot create a city admin without cities
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    When I register a city admin with cities ""
    Then the request is invalid

  Scenario: As an admin, I can create a city admin with cities
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    When I register a city admin with cities "Lille, Roubaix"
    Then I see a user
    And the JSON node roles should exist
    And the JSON node cities should exist

  Scenario: As a city admin, I can access a user in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    When I get user "foo@example.com"
    Then I see a user

  Scenario: As a city admin, I cannot access a user in another city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Paris |
    When I get user "foo@example.com"
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot access another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I get user "bar@example.com"
    Then I am forbidden to access this resource

  Scenario: As a user, I can create my profile through my account
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I create my profile
    Then I see a user
    And the JSON node profile should not be null
    And the JSON node profile.city should be equal to Lille

  Scenario: As a user, I can update my profile through my account
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And the following profile:
      | user            | city  |
      | foo@example.com | Paris |
    And I am authenticated as "foo@example.com"
    When I update my profile
    Then I see a user
    And the JSON node profile should not be null
    And the JSON node profile.city should be equal to Lille

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

  Scenario: As a city admin, I can update a user in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    When I update user "foo@example.com"
    Then I see a user

  Scenario: As a city admin, I cannot update a user in another city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Paris |
    When I update user "foo@example.com"
    Then I am forbidden to access this resource

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

  Scenario: As a city admin, I can delete a user in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    When I delete user "foo@example.com"
    Then the user "foo@example.com" has been successfully deleted

  Scenario: As a city admin, I cannot delete a user in another city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Paris |
    When I delete user "foo@example.com"
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot delete another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I delete user "bar@example.com"
    Then I am forbidden to access this resource

  @ko
  Scenario: As a user, I can delete my own account
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I delete user "foo@example.com"
    Then the user "foo@example.com" has been successfully deleted
    And I am authenticated as "foo@example.com"
    When I get user "foo@example.com"
    Then I am unauthorized to access this resource

  Scenario: As an admin, I can access a list of users
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And there are 3 users
    When I get a list of users
    Then I see a list of users

  Scenario: As a city admin, I can access a list of users in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
      | bar@example.com   | ROLE_USER       | true   |                |
      | lorem@example.com | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user              | city    |
      | foo@example.com   | Lille   |
      | bar@example.com   | Paris   |
      | lorem@example.com | Roubaix |
    When I get a list of users
    Then I see a list of users
    And the JSON node "hydra:totalItems" should be equal to 2

  Scenario: As a user, I cannot access a list of users
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And there are 3 users
    When I get a list of users
    Then I am forbidden to access this resource

  Scenario: As an admin, I can import a list of users
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And I add "Accept" header equal to "application/ld+json"
    And I add "Content-Type" header equal to "text/csv"
    When I send a POST request to "/users/import" with body:
    """
    email,active,plainPassword
    foo@example.com,true,fooPassword
    bar@example.com,1,barPassword
    john.doe@example.com,false,aPassword
    jane.doe@example.com,0,anotherPassword
    """
    Then I see a list of users
    And the JSON node "hydra:totalItems" should be equal to 4

  Scenario: As a city admin, I cannot import a list of users
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
    And I am authenticated as "admin@example.com"
    And I add "Accept" header equal to "application/ld+json"
    And I add "Content-Type" header equal to "text/csv"
    When I send a POST request to "/users/import" with body:
    """
    email,active,plainPassword
    foo@example.com,true,fooPassword
    bar@example.com,1,barPassword
    john.doe@example.com,false,aPassword
    jane.doe@example.com,0,anotherPassword
    """
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot import a list of users
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And I add "Accept" header equal to "application/ld+json"
    And I add "Content-Type" header equal to "text/csv"
    When I send a POST request to "/users/import" with body:
    """
    email,active,plainPassword
    foo@example.com,true,fooPassword
    bar@example.com,1,barPassword
    john.doe@example.com,false,aPassword
    jane.doe@example.com,0,anotherPassword
    """
    Then I am forbidden to access this resource

  Scenario: As anonymous, I can register
    When I register
    Then I see a user
    And I receive an email to validate my registration
    And user has been successfully created

  @ko
  Scenario: As anonymous, I can validate my registration
    Given the following user:
      | active | salt |
      | false  | foo  |
    When I validate my account
    Then I see a user
    And user has been validated

  Scenario: As an admin, I can create a user
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    When I register a user
    Then I see a user
    And user has been successfully created

  # todo Restrict by city?
  Scenario: As a city admin, I can create a user in my city
    Given the following user:
      | email             | roles           | active |
      | admin@example.com | ROLE_ADMIN_CITY | true   |
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

  @ko
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

  Scenario Outline: As a city admin, I can access a user quizzes in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city   |
      | foo@example.com | <city> |
    And there are valid quizzes
    And user "foo@example.com" has quizzes
    When I get user "foo@example.com" quizzes
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hydra:totalItems" should be equal to <count>
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
    Examples:
      | city    | count |
      | Lille   | 3     |
      | Roubaix | 3     |

  Scenario: As a city admin, I cannot access a user quizzes in another city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Paris |
    And there are valid quizzes
    And user "foo@example.com" has quizzes
    When I get user "foo@example.com" quizzes
    Then I am forbidden to access this resource

  @ko
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
              "@id": {"pattern": "^/choices/[\\w-]+$"},
              "@type": {"pattern": "^Choice$"},
              "question": {"pattern": "^/questions/[\\w-]+$"},
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

  Scenario: As an admin, I cannot update a UserQuiz
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And there are valid quizzes
    And user "foo@example.com" has quizzes
    And I am authenticated as "admin@example.com"
    When I update a userQuiz
    Then the method is not allowed

  @ko
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

  @ko
  Scenario: As a city admin, I can get user scores in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And there are valid quizzes
    And user "foo@example.com" has quizzes
    When I get user "foo@example.com" scores
    Then I see user scores

  @ko
  Scenario: As a city admin, I cannot get user scores in another city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Paris |
    And there are valid quizzes
    And user "foo@example.com" has quizzes
    When I get user "foo@example.com" scores
    Then I am forbidden to access this resource

  @ko
  Scenario: As a user, I can get my favorites
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    And user "foo@example.com" has favorites
    When I get user "foo@example.com" favorites
    Then I see the user's favorites

  Scenario: As a user, I cannot get another user favorites
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    And user "bar@example.com" has favorites
    When I get user "bar@example.com" favorites
    Then I am forbidden to access this resource

  Scenario: As an admin, I can get a user favorites
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    And user "foo@example.com" has favorites
    When I get user "foo@example.com" favorites
    Then I see the user's favorites

  Scenario: As a city admin, I can get a user favorites in my city
    Given the following user:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    And user "foo@example.com" has favorites
    When I get user "foo@example.com" favorites
    Then I see the user's favorites

  Scenario: As a city admin, I cannot get a user favorites in another city
    Given the following user:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Paris |
    And the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    And user "foo@example.com" has favorites
    When I get user "foo@example.com" favorites
    Then I am forbidden to access this resource

  @ko
  Scenario: As a user, I can add a favorite
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    When I add favorites
    Then I see a user
    And user has 3 favorites

  @ko
  Scenario: As an admin, I cannot add a favorite to a user
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    When I add favorites to "foo@example.com"
    Then I see a user
    And user has 0 favorites

  @ko
  Scenario: As a user, I cannot add a favorite to another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following contents:
      | published |
      | true      |
      | true      |
      | true      |
    When I add favorites to "bar@example.com"
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot export users
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
      | bar@example.com   | ROLE_USER  | true   |
    And I am authenticated as "foo@example.com"
    When I export users in CSV
    Then I am forbidden to access this resource

  Scenario: As an admin, I can export users
    Given the following users:
      | email             | roles      | active | cities |
      | admin@example.com | ROLE_ADMIN | true   |        |
      | foo@example.com   | ROLE_USER  | true   |        |
      | bar@example.com   | ROLE_USER  | true   |        |
    And the following profiles:
      | user            | firstName | lastName | familySize | nbAdults | nbChildren | nbBabies | nbPets | mobile | phone | address              | postcode | city  |
      | foo@example.com | John      | DOE      | 3          | 2        | 1          | 0        | 1      |        |       | 123 chemin du moulin | 75000    | Lille |
      | bar@example.com | Jane      | DOE      |            |          |            |          |        |        |       |                      |          |       |
    And I am authenticated as "admin@example.com"
    When I export users in CSV
    Then I get a list of users in CSV

  Scenario: As a city admin, I can export users in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
      | bar@example.com   | ROLE_USER       | true   |                |
      | lorem@example.com | ROLE_USER       | true   |                |
      | ipsum@example.com | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user              | firstName | lastName | familySize | nbAdults | nbChildren | nbBabies | nbPets | mobile | phone | address              | postcode | city  |
      | foo@example.com   | John      | DOE      | 3          | 2        | 1          | 0        | 1      |        |       | 123 chemin du moulin | 75000    | Lille |
      | bar@example.com   | Jane      | DOE      |            |          |            |          |        |        |       |                      |          | Paris |
      | lorem@example.com | Jane      | DOE      |            |          |            |          |        |        |       |                      |          |       |
    When I export users in CSV
    Then CSV should contain 1 lines
