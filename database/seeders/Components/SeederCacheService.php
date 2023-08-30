<?php

namespace Database\Seeders\Components;

use App\Models\Category;
use App\Services\Client\API\Product\ProductFilterService;

class SeederCacheService
{

    private ProductFilterService $productsService;

    public function __construct(ProductFilterService $productsService)
    {
        $this->productsService = $productsService;
    }

    public function caching(): void
    {
        $categories = Category::query()
            ->select('id', 'title', 'title_rus')
            ->get()
            ->each(function (Category $category) {
                $data = $this->productsService->getProductFilterAggregateDataCache([], $category);
                cache()->forever('first_page_product_aggregate_data_without_filter_by_category_id:' . $category->id, $data);
            });
        cache()->forever('categories', $categories->toArray());
    }
}
