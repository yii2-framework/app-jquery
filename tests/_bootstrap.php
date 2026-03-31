<?php

declare(strict_types=1);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require dirname(__DIR__) . '/vendor/autoload.php';

// Run migrations on the test database.
$app = new yii\console\Application(
    [
        'id' => 'app-basic-test-migrate',
        'basePath' => dirname(__DIR__),
        'aliases' => ['@app/Migrations' => dirname(__DIR__) . '/src/Migrations'],
        'components' => ['db' => require dirname(__DIR__) . '/config/test_db.php'],
        'controllerMap' => [
            'migrate' => [
                'class' => yii\console\controllers\MigrateController::class,
                'migrationNamespaces' => ['app\\Migrations'],
                'migrationPath' => null,
                'interactive' => false,
                'compact' => true,
            ],
        ],
    ],
);

ob_start();
$app->runAction('migrate/up');
ob_end_clean();

$app = null;

// @phpstan-ignore assign.propertyType
Yii::$app = null;
