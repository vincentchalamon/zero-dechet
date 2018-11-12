const { Given, Then, When } = require('cucumber');
const { login } = require('../pages');
const { assert, navigation } = require('../support');

When(/^I go to the homepage$/, async () => {
  await navigation.goto(login.url);
});

Then(/^I should see a login form$/, async () => {
  await assert.containsText(login.selectors.title, 'Coucou');
  await assert.containsText(login.selectors.submit, 'Se connecter');
});

When(/^I fill in and submit the login form$/, async () => {
  await navigation.authenticate();
});

When(/^I fill in and submit the login form with invalid credentials$/, async () => {
  await navigation.authenticate(false);
});

Then(/^I should see an error message on the login form$/, async () => {
  await assert.containsText(login.selectors.errorMessage, 'Email ou mot de passe invalide.');
});

Given(/^I am authenticated$/, async () => {
  await navigation.authenticate();
});

Then(/^I should be redirected to the homepage$/, async () => {
  await assert.containsText(login.selectors.title, 'Coucou');
  await assert.urlEquals(login.url);
});
