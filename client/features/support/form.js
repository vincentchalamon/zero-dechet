const scope = require('./scope');

const fillField = async (selector, value) => {
  const page = scope.context.currentPage;
  await page.click(selector);
  await page.keyboard.type(value);
};

const submit = async (selector, waitForNavigation = true) => {
  const page = scope.context.currentPage;
  await page.click(selector);
  if (waitForNavigation) {
    await page.waitForNavigation();
  }
};

module.exports = {
  fillField: fillField,
  submit: submit,
};
