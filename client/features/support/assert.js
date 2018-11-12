const assert = require('assert');
const scope = require('./scope');

const containsText = async (selector, value) => {
  const label = await scope.context.currentPage.$eval(selector, el => el.innerText);

  return await assert.equal(label.toString().trim(), value.toString().trim());
};

const urlEquals = async (url) => {
  const pageUrl = await scope.context.currentPage.url();

  return await assert.equal(pageUrl, scope.host + url);
};

module.exports = {
  containsText: containsText,
  urlEquals: urlEquals,
};
