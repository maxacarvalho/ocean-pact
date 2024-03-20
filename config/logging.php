<?php

return [

    'channels' => [
        'flare' => [
            'driver' => 'flare',
        ],

        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'flare'],
            'ignore_exceptions' => false,
        ],
    ],

];
