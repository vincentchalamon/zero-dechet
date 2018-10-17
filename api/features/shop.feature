Feature: CRUD Shop
  In order to use the Shop API
  As a user or an admin
  I need to be able to retrieve, create, update and delete Shop resources.

  Scenario: As an admin, I can get a list of shops
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following shops:
      | name                       | address                    | postcode | city  | coordinates                 |
      | Day by Day                 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | L'Epicerie Equitable       | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) |
      | Biocoop Vert'Tige Wazemmes | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   |
    When I get a list of shops
    Then I see a list of shops

  Scenario: As a user, I can get a list of shops
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following shops:
      | name                       | address                    | postcode | city  | coordinates                 |
      | Day by Day                 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | L'Epicerie Equitable       | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) |
      | Biocoop Vert'Tige Wazemmes | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   |
    When I get a list of shops
    Then I see a list of shops
    And the JSON node "hydra:totalItems" should be equal to 3

  Scenario Outline: As a user, I can get a list of shops filtered by tag
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following tags:
      | name       |
      | animalerie |
      | épicerie   |
      | cosmétique |
    And the following shops:
      | name                       | address                    | postcode | city  | coordinates                 | tags                             |
      | Day by Day                 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) | animalerie, épicerie, cosmétique |
      | L'Epicerie Equitable       | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) | animalerie, cosmétique           |
      | Biocoop Vert'Tige Wazemmes | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   | cosmétique                       |
    And I add Accept header equal to "application/ld+json"
    When I send a GET request to "/shops?<query>"
    Then I see a list of shops
    And the JSON node "hydra:totalItems" should be equal to <total>
    Examples:
      | query                                       | total |
      | tags.name[]=épicerie                        | 1     |
      | tags.name[]=animalerie                      | 2     |
      | tags.name[]=cosmétique                      | 3     |
      | tags.name[]=cosmétique&tags.name[]=épicerie | 1     |

  Scenario: As an admin, I can create a shop
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    When I create a shop with:
      | name               | address          | postcode | city  |
      | Supermarchés Match | 97 Rue Solférino | 59000    | Lille |
    Then I see a shop

  Scenario: As a user, I can create a shop
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    When I create a shop with:
      | name       | address               | postcode | city  |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille |
    Then I see a shop

  Scenario: As an admin, I can update a shop
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following shop:
      | name       | address               | postcode | city  | coordinates                 |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
    When I update a shop with:
      | name       | address               | postcode | city  |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille |
    Then I see a shop

  Scenario: As a city admin, I can update a shop in my city
    Given the following user:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
    And I am authenticated as "admin@example.com"
    And the following shop:
      | name       | address               | postcode | city  | coordinates                 |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
    When I update a shop with:
      | name       | address               | postcode | city  |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille |
    Then I see a shop

  Scenario: As a city admin, I cannot update a shop in another city
    Given the following user:
      | email             | roles           | active | cities |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Paris  |
    And I am authenticated as "admin@example.com"
    And the following shop:
      | name       | address               | postcode | city  | coordinates                 |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
    When I update a shop with:
      | name       | address               | postcode | city  |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille |
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot update a shop
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following shop:
      | name       | address               | postcode | city  | coordinates                 |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
    When I update a shop with:
      | name       | address               | postcode | city  |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille |
    Then I am forbidden to access this resource

  Scenario: As an admin, I can delete a shop
    Given the following user:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
    And I am authenticated as "admin@example.com"
    And the following shop:
      | name       | address               | postcode | city  | coordinates                 |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
    When I delete a shop
    Then the shop has been successfully deleted

  Scenario: As a city admin, I can delete a shop in my city
    Given the following user:
      | email             | roles           | active | cities         |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Lille, Roubaix |
    And I am authenticated as "admin@example.com"
    And the following shop:
      | name       | address               | postcode | city  | coordinates                 |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
    When I delete a shop
    Then the shop has been successfully deleted

  Scenario: As a city admin, I cannot delete a shop in another city
    Given the following user:
      | email             | roles           | active | cities |
      | admin@example.com | ROLE_ADMIN_CITY | true   | Paris  |
    And I am authenticated as "admin@example.com"
    And the following shop:
      | name       | address               | postcode | city  | coordinates                 |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
    When I delete a shop
    Then I am forbidden to access this resource

  Scenario: As a user, I cannot delete a shop
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following shop:
      | name       | address               | postcode | city  | coordinates                 |
      | Day by Day | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
    When I delete a shop
    Then I am forbidden to access this resource

  Scenario Outline: As a user, I can geocode shops around my position
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following shops:
      | name                       | address                    | postcode | city  | coordinates                 |
      | Day by Day                 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | L'Epicerie Equitable       | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) |
      | Biocoop Vert'Tige Wazemmes | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   |
      | La Maison du Zéro Déchet   | 3 Rue Charles Nodier       | 75018    | Paris | POINT(2.3447884 48.8854516) |
    When I find shops around 3.0527313,50.6309841 up to <distance> kilometers
    Then the response status code should be 200
    And the JSON node "hydra:totalItems" should be equal to <totalItems>

    Examples:
      | distance | totalItems |
      | 1        | 3          |
      | 300      | 4          |
