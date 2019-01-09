Feature: As a user, I can login to get resources

  Scenario: As anonymous, I cannot access any private resource
    When I get a list of users
    Then I am unauthorized to access this resource

  Scenario Outline: As anonymous, I can access API doc
    When I get the API doc in <format>
    Then I see the API doc in <format>
    Examples:
      | format |
      | json   |
      | jsonld |
      | html   |

  Scenario: I cannot access any private resource using fake authorization headers
    Given I add "Authorization" header equal to "Bearer foo"
    When I get a list of users
    Then I am unauthorized to access this resource

  Scenario: As a user, I can log in
    Given the following user:
      | email           | plainPassword | active |
      | foo@example.com | p4ssw0rd      | true   |
    When I log in with "foo@example.com" p4ssw0rd
    Then I get a valid token & user
    And the user is "foo@example.com"

  Scenario: As a user, I cannot log in if my account is disabled
    Given the following user:
      | email           | plainPassword | active |
      | foo@example.com | p4ssw0rd      | false  |
    When I log in with "foo@example.com" p4ssw0rd
    Then I am unauthorized to access this resource
