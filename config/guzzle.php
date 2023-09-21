<?php

return [
    'base_uri' => env('REDIS_HOST', env('APP_URL')),
    'timeout' => '2.0',
    'verify' => false,
];
