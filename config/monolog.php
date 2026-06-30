<?php

return [
    'mode' => 'nested',
    'drivers' => [
        'nested' => [
            'class' => Monolog\Formatter\JsonFormatter::class,
        ],
        'single' => [
            'class' => Monolog\Formatter\LineFormatter::class,
        ],
        'daily' => [
            'class' => Monolog\Formatter\LineFormatter::class,
        ],
    ],
];
