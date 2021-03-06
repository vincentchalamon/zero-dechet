Feature: CRUD Event
  In order to use the Event API
  As a user or an admin
  I need to be able to retrieve, create, update and delete Event resources.

  Scenario Outline: As any user, I can get a list of events
    Given the following user:
      | email            | roles  | active |
      | user@example.com | <role> | true   |
    And I am authenticated as "user@example.com"
    And the following events:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Intervention Alice     | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) |
    When I get a list of events
    Then I see a list of events
    And the JSON node "hydra:totalItems" should be equal to 2
    Examples:
      | role       |
      | ROLE_ADMIN |
      | ROLE_USER  |

  # todo Only send notification to users near event location
  Scenario Outline: As any user, I can create an event, a notification is sent to all active users
    Given the following users:
      | email            | roles     | active |
      | user@example.com | <role>    | true   |
      | foo@example.com  | ROLE_USER | true   |
      | bar@example.com  | ROLE_USER | false  |
    And I am authenticated as "user@example.com"
    When I create an event with:
      | title                  | address          | postcode | city  |
      | Conférence Béa Johnson | 97 Rue Solférino | 59000    | Lille |
    Then I see an event
    And 1 notification is sent to "foo@example.com"
    Examples:
      | role       |
      | ROLE_ADMIN |
      | ROLE_USER  |

  Scenario: As a user, I can create an event
    Given the following users:
      | email                | roles     | active |
      | foo@example.com      | ROLE_USER | true   |
      | bar@example.com      | ROLE_USER | true   |
      | inactive@example.com | ROLE_USER | false  |
    And I am authenticated as "foo@example.com"
    When I create an event with:
      | title                  | address          | postcode | city  |
      | Conférence Béa Johnson | 97 Rue Solférino | 59000    | Lille |
    Then I see an event
    And 1 notification is sent

  Scenario: As an admin, I can update any event
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | foo@example.com |
    When I update an event with:
      | title              | address          | postcode | city  |
      | Intervention Alice | 97 Rue Solférino | 59000    | Lille |
    Then I see an event
    And 1 notification is sent

  Scenario: As a user, I can update an event address I've created, a notification is sent to all pending/validated registration users
    Given the following users:
      | email             | roles     | active |
      | bar@example.com   | ROLE_USER | true   |
      | foo@example.com   | ROLE_USER | true   |
      | lorem@example.com | ROLE_USER | true   |
      | amet@example.com  | ROLE_USER | true   |
    And I am authenticated as "bar@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And user "foo@example.com" is registered to this event
    And user "lorem@example.com" is registered and validated to this event
    And user "amet@example.com" is registered and refused to this event
    When I update an event with:
      | title              | address          | postcode | city  |
      | Intervention Alice | 97 Rue Solférino | 59000    | Lille |
    Then I see an event
    And 1 notification is sent to "foo@example.com"
    And 1 notification is sent to "lorem@example.com"

  Scenario: As a user, I can update an event date I've created, a notification is sent to all pending/validated registration users
    Given the following users:
      | email             | roles     | active |
      | bar@example.com   | ROLE_USER | true   |
      | foo@example.com   | ROLE_USER | true   |
      | lorem@example.com | ROLE_USER | true   |
      | amet@example.com  | ROLE_USER | true   |
    And I am authenticated as "bar@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And user "foo@example.com" is registered to this event
    And user "lorem@example.com" is registered and validated to this event
    And user "amet@example.com" is registered and refused to this event
    When I update an event with:
      | startAt                   |
      | 2039-03-18T12:00:00+00:00 |
    Then I see an event
    And 1 notification is sent to "foo@example.com"
    And 1 notification is sent to "lorem@example.com"

  Scenario: As a user, I can update an event title I've created, 0 notification is sent
    Given the following users:
      | email           | roles     | active |
      | bar@example.com | ROLE_USER | true   |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "bar@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And user "foo@example.com" is registered to this event
    When I update an event with:
      | title |
      | Foo   |
    Then I see an event
    And 0 notification is sent

  Scenario: As an admin, I can delete any event, a notification is sent to the organizer
    Given the following users:
      | email             | roles      | active |
      | admin@example.com | ROLE_ADMIN | true   |
      | bar@example.com   | ROLE_USER  | true   |
      | foo@example.com   | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    When I delete an event
    Then the event has been successfully deleted
    And 1 notification is sent to "bar@example.com"

  Scenario: As a user, I can delete an event I've created, a notification is sent to all pending/validated registration users
    Given the following user:
      | email             | roles     | active |
      | bar@example.com   | ROLE_USER | true   |
      | foo@example.com   | ROLE_USER | true   |
      | lorem@example.com | ROLE_USER | true   |
      | amet@example.com  | ROLE_USER | true   |
    And I am authenticated as "bar@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And user "foo@example.com" is registered to this event
    And user "lorem@example.com" is registered and validated to this event
    And user "amet@example.com" is registered and refused to this event
    When I delete an event
    Then the event has been successfully deleted
    And 1 notification is sent to "foo@example.com"
    And 1 notification is sent to "lorem@example.com"

  Scenario: As a user, I cannot delete an event another user has created
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    When I delete an event
    Then I am forbidden to access this resource

  Scenario Outline: As a user, I can geocode events around my position
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following events:
      | title                    | startAt                   | endAt                     | address                    | postcode | city  | coordinates                 |
      | Conférence Béa Johnson   | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Intervention Alice       | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Le Commerce Equitable    | 2039-01-03T18:30:00+00:00 | 2039-01-03T20:30:00+00:00 | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) |
      | Qu'est-ce qu'une Biocoop | 2039-02-03T10:00:00+00:00 | 2039-02-03T14:00:00+00:00 | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   |
      | Conférence à Paris       | 2039-04-01T10:00:00+00:00 | 2039-04-01T14:00:00+00:00 | 3 Rue Charles Nodier       | 75018    | Paris | POINT(2.3447884 48.8854516) |
    When I find events around 3.0527313,50.6309841 up to <distance> kilometers
    Then the response status code should be 200
    And the JSON node "hydra:totalItems" should be equal to <totalItems>

    Examples:
      | distance | totalItems |
      | 1        | 4          |
      | 300      | 5          |

  Scenario: As a user, I can like an event
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    When I like an event
    Then I see an event

  Scenario: As a user, I cannot like an event I've already liked
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And user "foo@example.com" likes the event "Conférence Béa Johnson"
    When I like an event
    Then the response status code should be 400

  Scenario: As a user, I can register to an event, a notification is sent to the organizer
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    When I register to this event
    Then I see a registration
    And I am registered to this event
    And 1 notification is sent to "bar@example.com"

  Scenario: As a user, I cannot register to an event I'm already registered to
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And I'm registered to this event
    When I register to an event
    Then the request is invalid

  Scenario: As a user, I cannot register another user to an event
    Given the following users:
      | email              | roles     | active |
      | foo@example.com    | ROLE_USER | true   |
      | bar@example.com    | ROLE_USER | true   |
      | lipsum@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    When I register user "lipsum@example.com" to an event
    Then user "lipsum@example.com" is not registered to this event

  Scenario: As an admin, I can register another user to an event, a notification is sent to the organizer
    Given the following users:
      | email              | roles      | active |
      | admin@example.com  | ROLE_ADMIN | true   |
      | bar@example.com    | ROLE_USER  | true   |
      | lipsum@example.com | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    When I register user "lipsum@example.com" to an event
    Then I see a registration
    And user "lipsum@example.com" is successfully registered to this event
    And 1 notification is sent to "bar@example.com"

  Scenario: As an organizer, I cannot register to my event
    Given the following user:
      | email           | roles     | active |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "bar@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    When I register to an event
    Then the request is invalid

  Scenario: As a user, I cannot register to a past event
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2017-03-18T10:00:00+00:00 | 2017-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    When I register to an event
    Then the request is invalid

  Scenario: As a user, I can register to an event, my registration is automatically validated and I & the organizer receive a notification
    Given the following users:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
      | bar@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       | autoValidateRegistrations |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com | true                      |
    When I register to an event
    Then I see a registration
    And I am registered to this event
    And the registration is validated
    And 1 notification is sent to "foo@example.com"
    And 1 notification is sent to "bar@example.com"

  Scenario: As a user, I can register a number of attendees to an event with limit and my registration is pending, the organizer receive a notification
    Given the following users:
      | email              | roles     | active |
      | foo@example.com    | ROLE_USER | true   |
      | bar@example.com    | ROLE_USER | true   |
      | lipsum@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       | autoValidateRegistrations | limit |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com | true                      | 1     |
    And user "lipsum@example.com" is registered and validated to this event
    When I register 2 attendees to this event
    Then I see a registration
    And I am registered to this event
    And user "foo@example.com" registration is pending
    And 1 notification is sent to "bar@example.com"

  Scenario Outline: As a user, I can register to an event, but I'm often absent, so my registration is pending or validated (depending on my number of absences) and the organizer receive a notification
    Given the following users:
      | email           | roles     | active |
      | bar@example.com | ROLE_USER | true   |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following events:
      | title                    | description | startAt                   | endAt                     | address                    | postcode | city  | coordinates                 | organizer       | autoValidateRegistrations |
      | Intervention Alice       |             | 2017-05-03T10:00:00+00:00 | 2017-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com | true                      |
      | Le Commerce Equitable    |             | 2017-01-03T18:30:00+00:00 | 2017-01-03T20:30:00+00:00 | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) | bar@example.com | true                      |
      | Qu'est-ce qu'une Biocoop |             | 2017-02-03T10:00:00+00:00 | 2017-02-03T14:00:00+00:00 | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   | bar@example.com | true                      |
      | Conférence à Paris       |             | 2017-04-01T10:00:00+00:00 | 2017-04-01T14:00:00+00:00 | 3 Rue Charles Nodier       | 75018    | Paris | POINT(2.3447884 48.8854516) | bar@example.com | true                      |
      | Conférence Béa Johnson   |             | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com | true                      |
    And the following registrations:
      | user            | present   | event                    | status    |
      | foo@example.com | false     | Intervention Alice       | validated |
      | foo@example.com | false     | Le Commerce Equitable    | validated |
      | foo@example.com | false     | Qu'est-ce qu'une Biocoop | validated |
      | foo@example.com | <present> | Conférence à Paris       | validated |
    When I register to event "Conférence Béa Johnson"
    Then I see a registration
    And I am registered to this event
    And user "foo@example.com" registration is <status>
    And <count> notification is sent to "foo@example.com"
    And 1 notification is sent to "bar@example.com"
    Examples:
      | present | status    | count |
      | true    | validated | 1     |
      | false   | pending   | 0     |

  Scenario Outline: As an organizer, I can validate or refuse a registration to my event, a notification is sent to the user
    Given the following users:
      | email           | roles     | active |
      | bar@example.com | ROLE_USER | true   |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "bar@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And user "foo@example.com" is registered to this event
    When I <action> user "foo@example.com" registration
    Then I see a registration
    And the registration is <status>
    And 1 notification is sent to "foo@example.com"
    Examples:
      | status    | action   |
      | validated | validate |
      | refused   | refuse   |

  Scenario Outline: As an organizer, I cannot validate or refuse a registration to an event I've not organized
    Given the following users:
      | email             | roles     | active |
      | bar@example.com   | ROLE_USER | true   |
      | foo@example.com   | ROLE_USER | true   |
      | lorem@example.com | ROLE_USER | true   |
    And I am authenticated as "lorem@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And user "foo@example.com" is registered to this event
    When I <action> user "foo@example.com" registration
    Then I am forbidden to access this resource
    And the registration is pending
    And 0 notification is sent
    Examples:
      | action   |
      | validate |
      | refuse   |

  Scenario: As an admin, I cannot get all registrations
    Given the following users:
      | email              | roles      | active |
      | admin@example.com  | ROLE_ADMIN | true   |
      | bar@example.com    | ROLE_USER  | true   |
      | foo@example.com    | ROLE_USER  | true   |
      | lipsum@example.com | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And user "foo@example.com" is registered to this event
    And user "lipsum@example.com" is registered to this event
    And I add Accept header equal to "application/ld+json"
    When I send a GET request to "/registrations"
    Then the method is not allowed

  Scenario: As an admin, I can get an event registrations
    Given the following users:
      | email              | roles      | active |
      | admin@example.com  | ROLE_ADMIN | true   |
      | bar@example.com    | ROLE_USER  | true   |
      | foo@example.com    | ROLE_USER  | true   |
      | lipsum@example.com | ROLE_USER  | true   |
    And I am authenticated as "admin@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
      | Intervention Alice     | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
    And user "foo@example.com" is registered to event "Intervention Alice"
    And user "lipsum@example.com" is registered and refused to event "Conférence Béa Johnson"
    And user "bar@example.com" is registered to event "Conférence Béa Johnson"
    And user "foo@example.com" is registered and validated to event "Conférence Béa Johnson"
    When I get event "Conférence Béa Johnson" registrations
    Then I see a list of event registrations
    And the JSON node "hydra:totalItems" should be equal to 3

  Scenario: As an organizer, I can get my event registrations
    Given the following users:
      | email              | roles     | active |
      | bar@example.com    | ROLE_USER | true   |
      | foo@example.com    | ROLE_USER | true   |
      | lipsum@example.com | ROLE_USER | true   |
      | amet@example.com   | ROLE_USER | true   |
    And I am authenticated as "bar@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
      | Intervention Alice     | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | foo@example.com |
    And user "bar@example.com" is registered to event "Intervention Alice"
    And user "foo@example.com" is registered and validated to event "Conférence Béa Johnson"
    And user "lipsum@example.com" is registered and refused to event "Conférence Béa Johnson"
    And user "amet@example.com" is registered to event "Conférence Béa Johnson"
    When I get event "Conférence Béa Johnson" registrations
    Then I see a list of event registrations
    And the JSON node "hydra:totalItems" should be equal to 3

  Scenario: As an organizer, I can export my event registrations in CSV
    Given the following users:
      | email              | roles     | active |
      | bar@example.com    | ROLE_USER | true   |
      | foo@example.com    | ROLE_USER | true   |
      | lipsum@example.com | ROLE_USER | true   |
      | amet@example.com   | ROLE_USER | true   |
    And I am authenticated as "bar@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
      | Intervention Alice     | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | foo@example.com |
    And user "bar@example.com" is registered to event "Intervention Alice"
    And user "foo@example.com" is registered and validated to event "Conférence Béa Johnson"
    And user "lipsum@example.com" is registered and refused to event "Conférence Béa Johnson"
    And user "amet@example.com" is registered to event "Conférence Béa Johnson"
    When I export event "Conférence Béa Johnson" registrations in CSV
    Then CSV should contain 3 lines

  Scenario: As a user, I can get an event validated registrations
    Given the following users:
      | email              | roles     | active |
      | bar@example.com    | ROLE_USER | true   |
      | foo@example.com    | ROLE_USER | true   |
      | lipsum@example.com | ROLE_USER | true   |
      | amet@example.com   | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following event:
      | title                  | startAt                   | endAt                     | address               | postcode | city  | coordinates                 | organizer       |
      | Conférence Béa Johnson | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | bar@example.com |
      | Intervention Alice     | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta | 59000    | Lille | POINT(3.0477986 50.6266922) | foo@example.com |
    And user "bar@example.com" is registered to event "Intervention Alice"
    And user "foo@example.com" is registered and validated to event "Conférence Béa Johnson"
    And user "lipsum@example.com" is registered and refused to event "Conférence Béa Johnson"
    And user "amet@example.com" is registered to event "Conférence Béa Johnson"
    When I get event "Conférence Béa Johnson" registrations
    Then I see a list of event registrations
    And the JSON node "hydra:totalItems" should be equal to 1

  Scenario: As a user, I can export my events in webcal format
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And I am authenticated as "foo@example.com"
    And the following events:
      | title                    | description | startAt                   | endAt                     | address                    | postcode | city  | coordinates                 |
      | Conférence Béa Johnson   |             | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Intervention Alice       |             | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Le Commerce Equitable    |             | 2039-01-03T18:30:00+00:00 | 2039-01-03T20:30:00+00:00 | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) |
      | Qu'est-ce qu'une Biocoop |             | 2039-02-03T10:00:00+00:00 | 2039-02-03T14:00:00+00:00 | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   |
      | Conférence à Paris       |             | 2039-04-01T10:00:00+00:00 | 2039-04-01T14:00:00+00:00 | 3 Rue Charles Nodier       | 75018    | Paris | POINT(2.3447884 48.8854516) |
    And I'm registered and validated to these events
    When I export my events in webcal
    Then I see the event webcal

  Scenario: As anonymous, I can export a user events in webcal format with a valid key (required for sync.)
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And the following events:
      | title                    | description | startAt                   | endAt                     | address                    | postcode | city  | coordinates                 |
      | Conférence Béa Johnson   |             | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Intervention Alice       |             | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Le Commerce Equitable    |             | 2039-01-03T18:30:00+00:00 | 2039-01-03T20:30:00+00:00 | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) |
      | Qu'est-ce qu'une Biocoop |             | 2039-02-03T10:00:00+00:00 | 2039-02-03T14:00:00+00:00 | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   |
      | Conférence à Paris       |             | 2039-04-01T10:00:00+00:00 | 2039-04-01T14:00:00+00:00 | 3 Rue Charles Nodier       | 75018    | Paris | POINT(2.3447884 48.8854516) |
    And user "foo@example.com" is registered and validated to these events
    When I export a user events in webcal
    Then I see the event webcal

  Scenario: As anonymous, I cannot export a user events in webcal format without the key
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And the following events:
      | title                    | description | startAt                   | endAt                     | address                    | postcode | city  | coordinates                 |
      | Conférence Béa Johnson   |             | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Intervention Alice       |             | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Le Commerce Equitable    |             | 2039-01-03T18:30:00+00:00 | 2039-01-03T20:30:00+00:00 | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) |
      | Qu'est-ce qu'une Biocoop |             | 2039-02-03T10:00:00+00:00 | 2039-02-03T14:00:00+00:00 | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   |
      | Conférence à Paris       |             | 2039-04-01T10:00:00+00:00 | 2039-04-01T14:00:00+00:00 | 3 Rue Charles Nodier       | 75018    | Paris | POINT(2.3447884 48.8854516) |
    And user "foo@example.com" is registered and validated to these events
    When I export a user events in webcal without the key
    Then I am unauthorized to access this resource

  Scenario: As anonymous, I cannot export an invalid user events in webcal format
    Given the following user:
      | email           | roles     | active |
      | foo@example.com | ROLE_USER | true   |
    And the following events:
      | title                    | description | startAt                   | endAt                     | address                    | postcode | city  | coordinates                 |
      | Conférence Béa Johnson   |             | 2039-03-18T10:00:00+00:00 | 2039-03-18T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Intervention Alice       |             | 2039-05-03T10:00:00+00:00 | 2039-05-03T14:00:00+00:00 | 384 Rue Léon Gambetta      | 59000    | Lille | POINT(3.0477986 50.6266922) |
      | Le Commerce Equitable    |             | 2039-01-03T18:30:00+00:00 | 2039-01-03T20:30:00+00:00 | 22 Place Nouvelle Aventure | 59000    | Lille | POINT(3.0488861 50.6261559) |
      | Qu'est-ce qu'une Biocoop |             | 2039-02-03T10:00:00+00:00 | 2039-02-03T14:00:00+00:00 | 9 Place Nouvelle Aventure  | 59000    | Lille | POINT(3.049558 50.626873)   |
      | Conférence à Paris       |             | 2039-04-01T10:00:00+00:00 | 2039-04-01T14:00:00+00:00 | 3 Rue Charles Nodier       | 75018    | Paris | POINT(2.3447884 48.8854516) |
    And user "foo@example.com" is registered and validated to these events
    When I export an invalid user events in webcal
    Then the page is not found
