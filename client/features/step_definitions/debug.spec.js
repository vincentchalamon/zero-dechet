'use strict';

const {client} = require('nightwatch-cucumber');
const {After, Then} = require('cucumber');

After(() => client.execute(`
  localStorage.clear();
  sessionStorage.clear();
`).deleteCookies().refresh());

Then(/^print last response$/, function () {
    return client.source(function (result) {
        console.log(result.value);
    });
});

Then(/^print console$/, function () {
    return client.getLog('browser', function (logs) {
        if (0 === logs.length) {
            console.log('Console is empty');
        } else {
            logs.forEach(function (log) {
                console.log('[' + log.level + '] ' + log.message);
            });
        }
    });
});
