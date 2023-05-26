<?php

namespace App\Components;

use GuzzleHttp\Client;

class ImportDataClient
{
    public $client;

    /**
     * ImportDataClient constructor.
     * @param $client
     */

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => '172.28.64.1:8876',
            'timeout' => 2.0,
            'verify' => false,
        ]);
    }
}
