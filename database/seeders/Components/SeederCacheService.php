<?php

namespace Database\Seeders\Components;

use App\Models\Category;
use App\Services\Client\API\Product\ProductFilterService;

class SeederCacheService
{

    public function __construct(public readonly ProductFilterService $service)
    {
    }

    public function caching(): void
    {
        $categories = Category::query()
            ->select('id', 'title', 'title_rus')
            ->get()
            ->each(function (Category $category) {
                cache()->forget('first_page_product_aggregate_data_without_filter_by_category_id:' . $category->id);
                $this->service->getProductFilterAggregateDataCache([], $category);
            });
        cache()->forever('categories', $categories->toArray());
    }
}
