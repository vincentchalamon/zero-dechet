Feature:
  As any user,
  when I go to homepage,
  I see a resource.

  Scenario: I see the Book resource
    When I go to url "/"
    Then the element "h1" is visible
    And the element "h1" contains "Welcome to the API Platform demo!"
