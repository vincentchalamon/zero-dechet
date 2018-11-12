const scope = require('./scope');
const form = require('./form');
const { login } = require('../pages');

const goto = async (page = '') => {
  if (!scope.browser) {
    scope.browser = await scope.driver.launch({
      ignoreHTTPSErrors: true,
      args: ['--no-sandbox', '--headless', '--disable-gpu'],
    });
  }

  if (!scope.context.currentPage) {
    scope.context.currentPage = await scope.browser.newPage();
    scope.context.currentPage.setViewport({
      width: 800,
      height: 600,
    });
  }

  await scope.context.currentPage.goto(scope.host + page, {
    timeout: 5000,
    waitUntil: 'networkidle2',
  });
};

const authenticate = async (valid = true) => {
  await goto(login.url);
  const selectors = login.selectors;
  const page = scope.context.currentPage;
  await page.waitForSelector(selectors.submit, {
    visible: true,
    timeout: 5000,
  });
  await form.fillField(selectors.username, valid ? 'admin@example.com' : 'invalid@example.com');
  await form.fillField(selectors.password, 'p4ssw0rd');
  await form.submit(selectors.submit, valid);
  if (valid) {
    await page.waitForNavigation();
  }
};

module.exports = {
  goto: goto,
  authenticate: authenticate,
};
