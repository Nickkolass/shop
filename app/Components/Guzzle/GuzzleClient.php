<?php

namespace App\Components\Guzzle;

use GuzzleHttp\Client;

class GuzzleClient
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client(config('guzzle'));
    }

    public static function make(): self
    {
        return new self;
    }
}
