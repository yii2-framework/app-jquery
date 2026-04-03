# Testing

This package contains unit, functional, and acceptance test suites powered by
[Codeception PHP Testing Framework](https://codeception.com/).

Tests are located in the `tests` directory. They are developed with [Codeception PHP Testing Framework](https://codeception.com/).

By default, there are 3 test suites:

- `unit`
- `functional`
- `acceptance`

## Running all tests

```bash
vendor/bin/codecept run --env php-builtin
```

The command above will execute all test suites (unit, functional, and acceptance). Unit tests verify system components,
functional tests emulate web requests, and acceptance tests run against a real HTTP server.

## Acceptance tests

The `acceptance` suite is configured in `tests/acceptance.suite.yml`.

### PhpBrowser (default)

By default, acceptance tests use the `PhpBrowser` module and run against the built-in PHP web server started via the
`php-builtin` environment.

```bash
# run all tests with built-in web server
composer tests

# run acceptance tests only
vendor/bin/codecept run acceptance --env php-builtin
```

### WebDriver + Selenium

To run acceptance tests in a real browser, switch the `acceptance` suite to use the `WebDriver` module.
`tests/acceptance.suite.yml` contains an example WebDriver configuration (commented).

1. Download and start [Selenium Server](https://www.selenium.dev/downloads/).
2. Install the corresponding browser driver (for example [GeckoDriver](https://github.com/mozilla/geckodriver/releases) or
   [ChromeDriver](https://googlechromelabs.github.io/chrome-for-testing/)).
3. Update `tests/acceptance.suite.yml` to enable `WebDriver` and disable `PhpBrowser`.
4. Run:

```bash
vendor/bin/codecept run acceptance --env php-builtin
```

## Running tests with Docker

```bash
docker compose exec -T php vendor/bin/codecept build
docker compose exec -T php vendor/bin/codecept run
```

## Code coverage

Code coverage is configured in `codeception.yml`. Run your tests and collect coverage with:

```bash
# collect coverage for all tests
vendor/bin/codecept run --coverage --coverage-html --coverage-xml --env php-builtin

# collect coverage only for unit tests
vendor/bin/codecept run unit --coverage --coverage-html --coverage-xml --env php-builtin

# collect coverage for unit and functional tests
vendor/bin/codecept run functional,unit --coverage --coverage-html --coverage-xml --env php-builtin
```

You can see code coverage output under the `tests/support/output` directory.

## Automated coding standards

Run Easy Coding Standard with fixes:

```bash
composer ecs
```

## Static analysis

Run PHPStan:

```bash
composer static
```

## Passing extra arguments

Composer scripts support forwarding additional arguments using `--`.

Examples:

```bash
composer tests -- --filter LoginCest
composer static -- --memory-limit=1G
```

## Next steps

- 📚 [Installation Guide](installation.md)
- ⚙️ [Configuration Reference](configuration.md)
