<?php

return [
    'base_uri' => env('GUZZLE_HOST', 'host.docker.internal') . ':' . env('APP_PORT', '8876'),
    'timeout' => '2.0',
    'verify' => false,
];
