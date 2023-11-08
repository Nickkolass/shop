<?php

namespace App\Components\Disk;

use Arhitector\Yandex\Disk\Resource\Closed;

interface DiskClientInterface
{
    public function getResource(string $path, int $limit, int $offset): Closed;
}
