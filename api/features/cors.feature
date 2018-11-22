@ko
Feature: As an application, I can get an API resource

  Scenario: As an invalid application, I cannot get an API resource
    Given I add "Origin" header equal to "http://invalid"
    When I send a "OPTIONS" request to "/"
    Then the header "access-control-allow-methods" should be equal to "POST, PUT, GET, DELETE, OPTIONS"
    And the header "access-control-allow-headers" should be equal to "Accept, Content-Type"
    And the header "access-control-allow-origin" should be equal to "null"

  Scenario: As a valid application, I can get an API resource
    Given I add "Origin" header equal to "https://www.zero-dechet.app"
    When I send a "OPTIONS" request to "/"
    Then the header "access-control-allow-methods" should be equal to "POST, PUT, GET, DELETE, OPTIONS"
    And the header "access-control-allow-headers" should be equal to "Accept, Content-Type"
    And the header "access-control-allow-origin" should be equal to "https://www.zero-dechet.app"

  Scenario: As a valid application, I cannot use an invalid header
    Given I add "Origin" header equal to "https://www.zero-dechet.app"
    And I add "Access-Control-Request-Method" header equal to "GET"
    And I add "Access-Control-Request-Headers" header equal to "Foo"
    When I send a "OPTIONS" request to "/"
    Then the response status code should be 400
    And the response should be equal to
    """
    Unauthorized header foo
    """

  Scenario: As a valid application, I cannot use an invalid method
    Given I add "Origin" header equal to "https://www.zero-dechet.app"
    And I add "Access-Control-Request-Method" header equal to "PATCH"
    And I add "Access-Control-Request-Headers" header equal to "Accept, Content-Type"
    When I send a "OPTIONS" request to "/"
    Then the response status code should be 405
