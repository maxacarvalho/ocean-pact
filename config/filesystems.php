<?php

return [

    'disks' => [
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_S3_ACCESS_KEY_ID'),
            'secret' => env('AWS_S3_SECRET_ACCESS_KEY'),
            'region' => env('AWS_S3_DEFAULT_REGION'),
            'bucket' => env('AWS_S3_BUCKET'),
            'url' => env('AWS_S3_URL'),
            'endpoint' => env('AWS_S3_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_S3_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => env('AWS_S3_THROW', false),
        ],
    ],

];
