<?php

declare(strict_types=1);

use app\Models\User;
use yii\caching\FileCache;
use yii\jquery\Bootstrap;
use yii\log\FileTarget;
use yii\mail\MailerInterface;
use yii\symfonymailer\Mailer;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'app-basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        Bootstrap::class,
    ],
    'controllerNamespace' => 'app\\Controllers',
    'viewPath' => dirname(__DIR__) . '/resources/views',
    'container' => [
        'singletons' => [
            MailerInterface::class => [
                'class' => Mailer::class,
                // send all mails to a file by default.
                'useFileTransport' => true,
                'viewPath' => '@app/resources/mail',
            ],
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => dirname(__DIR__) . '/node_modules',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => MailerInterface::class,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => [
                        'error',
                        'warning',
                    ],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];

return $config;
