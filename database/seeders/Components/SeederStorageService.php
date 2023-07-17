<?php

namespace Database\Seeders\Components;

use Illuminate\Support\Facades\Storage;

class SeederStorageService
{
    public function storagePreparation(): void
    {
        Storage::deleteDirectory('/public/preview_images/');
        Storage::deleteDirectory('/public/product_images/');
        $filesPath = Storage::files('/public/factories/');
        foreach ($filesPath as $filePath) {
            Storage::copy($filePath, str_replace('ories', '', $filePath));
        }
    }
}
