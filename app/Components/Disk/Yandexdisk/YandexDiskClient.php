<?php

namespace App\Components\Disk\Yandexdisk;

use App\Components\Disk\DiskClientInterface;
use Arhitector\Yandex\Disk;
use Arhitector\Yandex\Disk\Resource\Closed;

class YandexDiskClient implements DiskClientInterface
{
    public Disk $disk;

    public function __construct()
    {
        $this->disk = new Disk(config('services.yandexdisk.oauth_token'));
    }

    public function getResource(string $path, int $limit = 20, int $offset = 0): Closed
    {
        return $this->disk->getResource($path, $limit, $offset);
    }
}
