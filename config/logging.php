<?php

use Logtail\Monolog\LogtailHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    'channels' => [
        'flare' => [
            'driver' => 'flare',
        ],

        'logtail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_LOGTAIL_HANDLER', LogtailHandler::class),
            'handler_with' => [
                'sourceToken' => env('LOGTAIL_TOKEN'),
                'level' => env('LOGTAIL_LEVEL', 'debug'),
            ],
        ],
    ],

];
