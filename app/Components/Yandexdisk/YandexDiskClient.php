<?php

namespace App\Components\Yandexdisk;

use Arhitector\Yandex\Disk;

class YandexDiskClient
{
    public Disk $disk;

    public function __construct()
    {
        $this->disk = new Disk(config('services.yandexdisk.oauth_token'));
    }

    public static function make(): self
    {
        return new self;
    }

}
