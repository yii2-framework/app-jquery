<?php

declare(strict_types=1);

use yii\caching\FileCache;
use yii\console\controllers\MigrateController;
use yii\console\controllers\ServeController;
use yii\log\FileTarget;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'app-basic-console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\\Commands',
    'aliases' => [
        '@app/Migrations' => dirname(__DIR__) . '/src/Migrations',
        '@bower' => '@vendor/bower-asset',
        '@npm'   => dirname(__DIR__) . '/node_modules',
        '@tests' => dirname(__DIR__) . '/tests',
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'app\\Migrations',
            ],
            'migrationPath' => null,
        ],
        'serve' => [
            'class' => ServeController::class,
            'docroot' => '@app/public',
        ],
    ],
    'components' => [
        'cache' => [
            'class' => FileCache::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];

return $config;
