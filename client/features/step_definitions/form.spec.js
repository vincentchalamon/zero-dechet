'use strict';

const {client} = require('nightwatch-cucumber');
const {Then, When} = require('cucumber');

const defaultTimeout = 1000;

When(/^I fill in the field "([^"]*)" with "([^"]*)"$/, (selector, value) => {
    return client.setValue(selector, value);
});

Then(/^the field "([^"]*)" is empty$/, (selector) => {
    return !client.getValue(selector);
});

When(/^I submit the form$/, () => {
    return client
        .useXpath()
        .click("//form//button[@type='submit']")
        .useCss()
        .waitForElementNotPresent('form', defaultTimeout);
});
