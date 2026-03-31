<?php

declare(strict_types=1);

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
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => ['app\\Migrations'],
            'migrationPath' => null,
        ],
        'serve' => [
            'class' => \yii\console\controllers\ServeController::class,
            'docroot' => '@app/public',
        ],
    ],
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];

return $config;
