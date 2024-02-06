<?php

namespace App\Components\Transport\Protokol;

interface TransportInterface
{
    public function publish(): void;
}
