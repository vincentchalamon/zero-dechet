## Reporting bugs

If you happen to find a bug, we kindly request you to report it using GitHub by following these 3 points:

-   Check if the bug is not already reported
-   A clear title to resume the issue
-   A description of the workflow needed to reproduce the bug

> _NOTE:_ Don't hesitate giving as much information as you can (OS, PHP version extensions...)

## Git

It is recommended to have a global `.gitignore` file as described in https://help.github.com/articles/ignoring-files/.
Here is an example: https://gist.github.com/vincentchalamon/d5defad563ed49d9306a4aa57dfd4498

### Sign commits

**It is highly recommended to sign commits using GPG**: https://help.github.com/articles/signing-commits-using-gpg/

### Pull Requests

When you send a Pull Request, just make sure that:

-   It is readable (no more than 1000 lines modified, split your commits...)
-   You add valid test cases (unit & functional)
-   Tests are green
-   You make the Pull Request on dev
-   You specify the status of your Pull Request: `WIP` (Work In Progress) or `RFR` (Ready For Review)
-   Your code respect [SOLID principles](<https://en.wikipedia.org/wiki/SOLID_(object-oriented_design)>)

## Matching coding standards

The API follows [Symfony coding standards](https://symfony.com/doc/current/contributing/code/standards.html).
But don't worry, you can fix CS issues automatically using the [PHP CS Fixer](http://cs.sensiolabs.org/) tool already
installed in the project:

```bash
bin/php-cs-fixer fix src
```

And then, add fixed file to your commit before push. Be sure to add only **your modified files**. If another files are
fixed by cs tools, just revert it before commit.

## UUID

The API entities identifiers are managed by UUID in doctrine. Please read the documentation of the doctrine bridge on
[ramsey/uuid-doctrine](https://github.com/ramsey/uuid-doctrine).

## API tests

There are two kinds of tests in the API: unit (`phpunit`) and integration tests (`behat`).

Both `phpunit` and `behat` are development dependencies and should be available in the `vendor` directory.

#### PHPUnit and coverage generation

To launch unit tests:

```bash
bin/phpunit
```

If you want coverage, you will need the `phpdbg` package and run:

```bash
phpdbg -qrr bin/phpunit --coverage-html coverage
```

Sometimes there might be an error with too many open files when generating coverage. To fix this, you can increase the
`ulimit`, for example:

```bash
ulimit -n 4000
```

Coverage will be available in `coverage/index.html`.

#### Behat

To launch Behat tests:

```bash
bin/behat
```

#### Doctrine schema validation

To analyse your Doctrine schema, use:

```bash
bin/console doctrine:schema:validate --skip-sync
```

#### Security checker

To check security issues in project dependencies, use:

```bash
bin/security-checker security:check
```

## Doctrine migrations

Here we use the doctrine migrations bundle to manage the database's schema.

To generate a migration version file, use the following command:

```bash
bin/console doctrine:migrations:diff
```

To generate a blank migration file:

```bash
bin/console doctrine:migrations:generate
```

To execute the migrations:

```bash
bin/console doctrine:migrations:migrate
```

To see the complete documentation: https://symfony.com/doc/master/bundles/DoctrineMigrationsBundle/index.html

## Doctrine extensions

To use the doctrine extension bundle, you have to enable each extension you need in the `app/config.yml` file.

See details at the documentation [https://github.com/Atlantic18/DoctrineExtensions](https://github.com/Atlantic18/DoctrineExtensions).

## App tests

#### Jest and coverage generation

To launch unit tests:

```bash
yarn jest
```

If you want coverage, add `--coverage` option:

```bash
yarn jest --coverage
```

Coverage will be available in `coverage/clover.xml`.

#### E2E

To launch e2e tests (requires Android/iOS emulator):

```bash
yarn detox
```

#### ESLint

To launch eslint analysis:

```bash
yarn eslint src
```

#### Run the app on your device

Using [expo](https://expo.io), you can run the app on your device (require to install expo on your device):

```bash
yarn start
```

Then, scan the QR code, the app will be downloaded & synched, and start on your device.
