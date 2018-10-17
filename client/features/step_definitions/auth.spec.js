'use strict';

const {client} = require('nightwatch-cucumber');
const {Given} = require('cucumber');

Given(/^I am authenticated$/, () => {
    return client
        .url(client.launch_url + '/login')
        .waitForElementVisible('form', 1000)
        .setValue('input[name=email]', 'admin@example.com')
        .setValue('input[name=password]', 'p4ssw0rd')
        .submitForm('form')
        .waitForElementNotPresent('form', 1000);
});
