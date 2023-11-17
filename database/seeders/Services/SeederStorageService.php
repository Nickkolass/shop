<?php

namespace Database\Seeders\Services;

use App\Models\Category;
use App\Services\Client\API\Product\ProductFilterService;
use Illuminate\Support\Facades\Storage;

class SeederStorageService
{
    public function storagePreparation(): void
    {
        Storage::deleteDirectory('preview_images');
        Storage::deleteDirectory('product_images');
        Storage::deleteDirectory('comment_images');
        cache()->flush();
        cache()->put('imageCounter', 0);
        cache()->put('factory', Storage::disk('testing')->files('factory'));
    }

    public function caching(): void
    {
        if (Storage::getDefaultDriver() == 'public') shell_exec('chmod 777 -R ./storage/app/public');
        cache()->flush();
        $categories = Category::query()
            ->select('id', 'title')
            ->get()
            ->each(fn(Category $category) => (new ProductFilterService)->getProductFilterAggregateDataCache([], $category));
        cache()->forever('categories', $categories->toArray());
    }
}
