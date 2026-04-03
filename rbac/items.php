<?php

declare(strict_types=1);

return [
    'admin' => [
        'type' => 1,
        'description' => 'Administrator',
        'children' => [
            'viewUsers',
        ],
    ],
    'viewUsers' => [
        'type' => 2,
        'description' => 'View the users grid',
    ],
];
