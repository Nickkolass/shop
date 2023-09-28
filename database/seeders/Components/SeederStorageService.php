<?php

namespace Database\Seeders\Components;

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
        cache()->flush();
        $categories = Category::query()
            ->select('id', 'title', 'title_rus')
            ->get()
            ->each(fn(Category $category) => (new ProductFilterService)->getProductFilterAggregateDataCache([], $category));
        cache()->forever('categories', $categories->toArray());
    }

}
