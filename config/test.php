<?php

declare(strict_types=1);

use app\models\User;
use app\tests\support\MailerBootstrap;
use yii\jquery\Bootstrap;
use yii\symfonymailer\Mailer;
use yii\symfonymailer\Message;

$params = require __DIR__ . '/params.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'app-jquery-tests',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\\controllers',
    'viewPath' => dirname(__DIR__) . '/resources/views',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => dirname(__DIR__) . '/node_modules',
    ],
    'bootstrap' => [
        Bootstrap::class,
        MailerBootstrap::class,
    ],
    'language' => 'en-US',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../public/assets',
        ],
        'db' => require __DIR__ . '/test_db.php',
        'mailer' => [
            'class' => Mailer::class,
            'messageClass' => Message::class,
            'useFileTransport' => true,
            'viewPath' => '@app/resources/mail',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => User::class,
        ],
    ],
    'params' => $params,
];
