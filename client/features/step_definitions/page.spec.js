'use strict';

const {client} = require('nightwatch-cucumber');
const {Then, When} = require('cucumber');

const defaultTimeout = 1000;

When(/^I go to url "([^"]*)"$/, (url) => {
    return client
        .url(client.launch_url + url)
        .waitForElementVisible('body', defaultTimeout);
});

Then(/^I am redirected to "([^"]*)"$/, (url) => {
    return client.assert.urlEquals(client.launch_url + url);
});

When(/^I click on "([^"]*)"$/, (selector) => {
    return client.click(selector);
});

Then(/^the element "([^"]*)" contains "([^"]*)"$/, (selector, text) => {
    return client.assert.containsText(selector, text);
});

Then(/^the page is not found$/, () => {
    return client.assert.containsText('body', 'Page introuvable');
});

Then(/^the element "([^"]*)" is visible$/, (selector) => {
    return client.isVisible(selector);
});

Then(/^the element "([^"]*)" is not visible$/, (selector) => {
    return !client.isVisible(selector);
});

Then(/^I see a list of (\d+)(?: "([^"]*)")? elements?$/, (numberOfElements, element = 'li') => {
    return client
        .useCss('ul')
        .source(result => {
            let html = result.value;
            let count = 0;
            let pos = html.indexOf(element);
            while (pos !== -1) {
                count++;
                pos = html.indexOf(element, pos + 1);
            }
            client.assert.equal(parseInt(numberOfElements), count);
        });
});

When(/^I wait (\d+) second(?:s)?$/, (time) => {
    return client.pause(time*1000);
});

When(/^I wait "(\d+)" ms$/, (time) => {
    return client.timeouts('implicit', time);
});
