# Configuration reference

## Overview

The jQuery Application Template ships with sensible defaults. The main configuration files live under the `config/`
directory. Customize database connections, application components, and environment-specific settings there.

## Directory structure

```text
config/             contains application configurations
public/             contains the entry script and Web resources
resources/
    mail/           contains view files for e-mails
    views/          contains view files for the Web application
runtime/            contains files generated during runtime
src/
    assets/         contains assets definition
    commands/       contains console commands (controllers)
    controllers/    contains Web controller classes
    migrations/     contains database migrations
    models/         contains model classes
    widgets/        contains widget classes
tests/
    acceptance/     contains acceptance tests
    functional/     contains functional tests
    support/        contains test infrastructure and fixtures
    unit/           contains unit tests
vendor/             contains dependent 3rd-party packages
```

## Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**Notes:**

- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the readme in the `tests` directory for information specific to jQuery application tests.

## Next steps

- 📚 [Installation Guide](installation.md)
- 🧪 [Testing Guide](testing.md)
