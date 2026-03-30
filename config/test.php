<?php

$params = require __DIR__ . '/params.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'app-basic-tests',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\\Controllers',
    'viewPath' => dirname(__DIR__) . '/resources/views',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => dirname(__DIR__) . '/node_modules',
    ],
    'bootstrap' => [
        \yii\jquery\Bootstrap::class,
        \app\tests\Support\MailerBootstrap::class,
    ],
    'language' => 'en-US',
    'components' => [
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'messageClass' => \yii\symfonymailer\Message::class,
            'useFileTransport' => true,
            'viewPath' => '@app/resources/mail',
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../public/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => \app\Models\User::class,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
    ],
    'params' => $params,
];
