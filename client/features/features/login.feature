Feature:
  As a user,
  I see a login form if I'm anonymous,
  or the dashboard if I'm authenticated.

  Scenario: As anonymous user, when I log in with invalid credentials, I see an error message
    When I go to the homepage
    Then I should see a login form
    When I fill in and submit the login form with invalid credentials
    Then I should see an error message on the login form

  Scenario: As anonymous user, when I log in, I'm redirected to the dashboard
    When I go to the homepage
    Then I should see a login form
    When I fill in and submit the login form
    Then I should be redirected to the dashboard

  Scenario: As authenticated user, when I try to log in, I'm redirected to the dashboard
    Given I am authenticated
    When I go to the homepage
    Then I should be redirected to the dashboard
