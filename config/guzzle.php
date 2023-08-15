<?php

return [
    'base_uri' => env('REDIS_HOST', '127.0.0.1') . ':' . env('BASE_PORT', '8876'),
    'timeout' => '2.0',
    'verify' => false,
];
