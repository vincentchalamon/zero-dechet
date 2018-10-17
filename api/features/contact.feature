Feature: As a user, I can contact the webmaster

  Scenario: As anonymous, I cannot contact the webmaster
    When I create a contact
    Then I am unauthorized to access this resource

  Scenario: As user, I can contact the webmaster
    Given the following users:
      | email              | roles           | active | cities         |
      | admin@example.com  | ROLE_ADMIN      | true   |                |
      | admin2@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | admin3@example.com | ROLE_ADMIN_CITY | true   | Roubaix        |
      | foo@example.com    | ROLE_USER       | true   |                |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user               | city  |
      | admin@example.com  | Lille |
      | admin2@example.com | Lille |
      | admin3@example.com | Paris |
      | foo@example.com    | Lille |
    When I create a contact
    Then I see a contact
    And 2 mails should be sent

  Scenario: As admin, I can get a list of messages
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following contacts:
      | user              |
      | admin@example.com |
      | admin@example.com |
      | admin@example.com |
    When I get a list of contacts
    Then I see a list of contacts
    And the JSON node "hydra:totalItems" should be equal to 3

  Scenario: As a city admin, I can get a list of messages of users in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
      | bar@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And the following contacts:
      | user            |
      | foo@example.com |
      | bar@example.com |
      | foo@example.com |
    When I get a list of contacts
    Then I see a list of contacts
    And the JSON node "hydra:totalItems" should be equal to 2

  Scenario Outline: As admin, I can get a list of messages filtered by status
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following contacts:
      | user              | status |
      | admin@example.com | read   |
      | admin@example.com | read   |
      | admin@example.com | unread |
    When I add Accept header equal to "application/ld+json"
    Then I send a GET request to "/contacts?status=<status>"
    And the JSON node "hydra:totalItems" should be equal to <total>
    Examples:
      | status | total |
      | unread | 1     |
      | read   | 2     |
