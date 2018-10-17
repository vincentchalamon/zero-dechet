Feature: CRUD Weighing
  In order to use the Weighing API
  As a user or an admin
  I need to be able to retrieve, create, update and delete Weighing resources.

  Scenario: As an admin, I can get a list of weighings
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
      | bar@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And the following weighings:
      | user            | type           | total |
      | bar@example.com | recyclable     | 10    |
      | bar@example.com | non-recyclable | 5     |
      | bar@example.com | biodegradable  | 3     |
      | foo@example.com | recyclable     | 20    |
      | foo@example.com | non-recyclable | 7     |
      | foo@example.com | biodegradable  | 2     |
    When I get a list of weighings
    Then I see a list of weighings data
    And the JSON node "hydra:totalItems" should be equal to 6
    And the JSON node "totalWeight" should be equal to 47
    And the JSON node "totalRecyclableWeight" should be equal to 30
    And the JSON node "totalNonRecyclableWeight" should be equal to 12
    And the JSON node "totalBioWeight" should be equal to 5
    And the JSON node "averageWeight" should be equal to 7.8

  Scenario Outline: As a user, I can get a filtered list of weighings data
    Given the following user:
      | email                 | roles           | active |
      | admin@example.com     | ROLE_ADMIN      | true   |
      | adminCity@example.com | ROLE_ADMIN_CITY | true   |
      | foo@example.com       | ROLE_USER       | true   |
      | bar@example.com       | ROLE_USER       | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And the following teams:
      | name | users                            |
      | Bar  | foo@example.com, bar@example.com |
      | Foo  | foo@example.com                  |
    And the following weighings:
      | user            | type           | total |
      | bar@example.com | recyclable     | 10    |
      | bar@example.com | non-recyclable | 5     |
      | bar@example.com | biodegradable  | 3     |
      | foo@example.com | recyclable     | 20    |
      | foo@example.com | non-recyclable | 7     |
      | foo@example.com | biodegradable  | 2     |
      | foo@example.com | recyclable     | 10    |
      | foo@example.com | non-recyclable | 5     |
      | foo@example.com | biodegradable  | 3     |
    When I get weighings filtered by <filter> "<value>"
    Then I see a list of weighings data
    And the JSON node "hydra:totalItems" should be equal to 6
    And the JSON node "totalWeight" should be equal to 47
    And the JSON node "totalRecyclableWeight" should be equal to 30
    And the JSON node "totalNonRecyclableWeight" should be equal to 12
    And the JSON node "totalBioWeight" should be equal to 5
    And the JSON node "averageWeight" should be equal to 7.8
    Examples:
      | filter | value           |
      | user   | foo@example.com |
      | city   | Lille           |
      | team   | Foo             |

  Scenario: As a city admin, I can get a list of weighings of users in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
      | bar@example.com   | ROLE_USER       | true   |                |
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And I am authenticated as "admin@example.com"
    And the following weighings:
      | user            | type       |
      | foo@example.com | recyclable |
      | bar@example.com | recyclable |
      | bar@example.com | recyclable |
    When I get a list of weighings
    Then I see a list of weighings
    And the JSON node "hydra:totalItems" should be equal to 1

  Scenario: As a user, I can get a list of my weighings
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And the following weighings:
      | user            | type       |
      | foo@example.com | recyclable |
      | foo@example.com | recyclable |
      | bar@example.com | recyclable |
    When I get a list of weighings
    Then I see a list of weighings
    And the JSON node "hydra:totalItems" should be equal to 2

  Scenario: As an admin, I can get a weighing
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I get a weighing
    Then I see a weighing

  Scenario: As a user, I can get a weighing
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I get a weighing
    Then I see a weighing

  Scenario: As a user, I cannot get a weighing from another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And the following weighing:
      | user            | type       |
      | bar@example.com | recyclable |
    When I get a weighing
    Then I am forbidden to access this resource

  Scenario: As a city admin, I can get a weighing from a user in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I get a weighing
    Then I see a weighing

  Scenario: As a city admin, I cannot get a weighing from a user in another city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Paris |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I get a weighing
    Then I am forbidden to access this resource

  Scenario: As a user, I can create a weighing if I've filled my profile
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    When I create a weighing with:
      | type       |
      | recyclable |
    Then I see a weighing

  Scenario: As an admin, I can update a weighing
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I update a weighing with:
      | type           |
      | non-recyclable |
    Then I see a weighing

  Scenario: As a user, I can update my weighing
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I update a weighing with:
      | type           |
      | non-recyclable |
    Then I see a weighing

  Scenario: As a user, I cannot update a weighing from another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | bar@example.com | recyclable |
    When I update a weighing with:
      | type           |
      | non-recyclable |
    Then I am forbidden to access this resource

  Scenario: As a city admin, I can update a weighing from a user in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I update a weighing with:
      | type           |
      | non-recyclable |
    Then I see a weighing

  Scenario: As a city admin, I cannot update a weighing from a user in another city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Paris |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I update a weighing with:
      | type           |
      | non-recyclable |
    Then I am forbidden to access this resource

  Scenario: As an admin, I can delete a weighing
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I delete a weighing
    Then the weighing has been successfully deleted

  Scenario: As a user, I can delete my weighing
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I delete a weighing
    Then the weighing has been successfully deleted

  Scenario: As a user, I cannot delete a weighing from another user
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And the following weighing:
      | user            | type       |
      | bar@example.com | recyclable |
    When I delete a weighing
    Then I am forbidden to access this resource

  Scenario: As a city admin, I can delete a weighing from a user in my city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I delete a weighing
    Then the weighing has been successfully deleted

  Scenario: As a city admin, I cannot delete a weighing from a user in another city
    Given the following users:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
      | foo@example.com   | ROLE_USER       | true   |                |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Paris |
    And the following weighing:
      | user            | type       |
      | foo@example.com | recyclable |
    When I delete a weighing
    Then I am forbidden to access this resource

  Scenario: As a user, I can export all my weighings
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And the following weighings:
      | user            | type           |
      | bar@example.com | recyclable     |
      | bar@example.com | non-recyclable |
      | bar@example.com | biodegradable  |
      | foo@example.com | recyclable     |
      | foo@example.com | non-recyclable |
      | foo@example.com | biodegradable  |
    When I export weighings in CSV
    Then CSV should contain 3 lines

  Scenario: As an admin, I can export all weighings
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
      | bar@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And the following weighings:
      | user            | type           |
      | bar@example.com | recyclable     |
      | bar@example.com | non-recyclable |
      | bar@example.com | biodegradable  |
      | foo@example.com | recyclable     |
      | foo@example.com | non-recyclable |
      | foo@example.com | biodegradable  |
    When I export weighings in CSV
    Then CSV should contain 6 lines

  Scenario: As a city admin, I can export all weighings for my city
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
    And the following weighings:
      | user            | type           |
      | bar@example.com | recyclable     |
      | bar@example.com | non-recyclable |
      | bar@example.com | biodegradable  |
      | foo@example.com | recyclable     |
      | foo@example.com | non-recyclable |
      | foo@example.com | biodegradable  |
    When I export weighings in CSV
    Then CSV should contain 3 lines

  Scenario: As an admin, I can export all weighings for a user
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
      | bar@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following profiles:
      | user            | city  |
      | foo@example.com | Lille |
      | bar@example.com | Paris |
    And the following weighings:
      | user            | type           |
      | bar@example.com | recyclable     |
      | bar@example.com | non-recyclable |
      | bar@example.com | biodegradable  |
      | foo@example.com | recyclable     |
      | foo@example.com | non-recyclable |
      | foo@example.com | biodegradable  |
    When I export weighings in CSV for user "foo@example.com"
    Then CSV should contain 3 lines

  Scenario: As a city admin, I can export all weighings for a user in my city
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
    And the following weighings:
      | user            | type           |
      | bar@example.com | recyclable     |
      | bar@example.com | non-recyclable |
      | bar@example.com | biodegradable  |
      | foo@example.com | recyclable     |
      | foo@example.com | non-recyclable |
      | foo@example.com | biodegradable  |
    When I export weighings in CSV for user "foo@example.com"
    Then CSV should contain 3 lines

  Scenario: As a city admin, I cannot export all weighings for a user in another city
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
    And the following weighings:
      | user            | type           |
      | bar@example.com | recyclable     |
      | bar@example.com | non-recyclable |
      | bar@example.com | biodegradable  |
      | foo@example.com | recyclable     |
      | foo@example.com | non-recyclable |
      | foo@example.com | biodegradable  |
    When I export weighings in CSV for user "bar@example.com"
    Then I am forbidden to access this resource
