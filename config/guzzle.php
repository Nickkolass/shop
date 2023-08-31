<?php

return [
    'base_uri' => env('REDIS_HOST', 'host.docker.internal') . ':' . env('BASE_PORT', '8876'),
    'timeout' => '2.0',
    'verify' => false,
];
