Feature:
  In order to reset my password
  As a user
  I need to be able to ask for a token, and set a new password.

  Background:
    Given the following user:
      | email           | plainPassword | active |
      | foo@example.com | p4ssw0rd      | true   |

  Scenario: I can reset my password
    When I reset my password
    Then 1 mail should be sent

  Scenario: I can reset my password even if I have already requested a token and this token has not expired yet
    Given I have a valid token
    When I reset my password
    Then 1 mail should be sent

  Scenario: I can reset my password if I already request a token but it has expired
    Given I have an expired token
    When I reset my password
    Then 1 mail should be sent

  Scenario: I cannot reset my password with an invalid email address
    When I reset my password using invalid email address
    Then the response should be empty

  Scenario: I cannot reset my password with no email address specified
    When I reset my password using no email address
    Then the request is invalid

  Scenario: I cannot update my password using an invalid token
    When I update my password using an invalid token
    Then the page is not found

  Scenario: I cannot update my password using an expired token
    When I update my password using an expired token
    Then the page is not found

  Scenario: I cannot update my password with no password specified
    When I update my password using no password
    Then the request is invalid

  Scenario: I cannot update my password using a valid token but an invalid password
    When I set a new invalid password
    Then the request is invalid

  Scenario: I can update my password using a valid token and an valid password
    When I set a new password
    Then my password has been updated
    When I log in with "foo@example.com" loremipsum
    Then I get a valid token & user

  Scenario: I can get a password token
    When I get a password token
    Then I see a password token

  Scenario: I cannot get an expired password token
    When I get a password token using an expired token
    Then the page is not found
