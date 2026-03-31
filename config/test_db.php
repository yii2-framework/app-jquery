<?php

declare(strict_types=1);

return [
    'class' => \yii\db\Connection::class,
    'dsn' => 'sqlite:' . dirname(__DIR__) . '/tests/Support/data/test.sqlite',
];
