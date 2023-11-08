<?php

namespace App\Services\Client\API\Product;

use App\Http\Filters\ProductFilter;
use App\Http\Filters\ProductTypeFilter;
use App\Models\Category;
use App\Models\OptionValue;
use App\Models\ProductType;
use App\Models\PropertyValue;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;

class ProductFilterService
{

    /**
     * @param array<mixed> $data
     * @param Category $category
     * @return array<mixed>
     */
    public function getProductFilterAggregateDataCache(array $data, Category $category): array
    {
        if (!empty($data)) $result = $this->getProductFilterAggregateData($data, $category);
        else $result = cache()->rememberForever('first_page_product_aggregate_data_without_filter_by_category_id:' . $category->id,
            fn() => $this->getProductFilterAggregateData($data, $category));
        return $result;
    }

    /**
     * @param array<mixed> $data
     * @param Category $category
     * @return array<mixed>
     */
    private function getProductFilterAggregateData(array $data, Category $category): array
    {
        $data['filter'] = $data['filter'] ?? [];
        $data['category'] = $category;
        $this->fillDataPaginate($data)->fillDataFilterable($data)->fillDataFilteredProductTypes($data);
        return $data;
    }

    /**
     * @param array<mixed> &$data
     * @return void
     */
    private function fillDataFilteredProductTypes(array &$data): void
    {
        $data['productTypes'] = ProductType::query()
            ->filter(new ProductTypeFilter($data['filter']))
            ->sort($data['paginate']['orderBy'])
            ->withWhereHas('product', function ($q) use ($data) {
                $q->filter(new ProductFilter($data['filter'] + ['category' => $data['category']->id]))
                    ->select('products.id', 'title', 'rating', 'count_comments', 'count_rating')
                    ->with('productTypes:id,product_id,is_published,preview_image');
            })
            ->select('productTypes.id', 'productTypes.product_id', 'is_published', 'preview_image', 'price', 'count')
            ->withExists(['liked' => fn(Builder $q) => $q->where('user_id', auth('api')->id())])
            ->with([
                'productImages:productType_id,file_path',
                'optionValues.option:id,title',
            ])
            ->simplePaginate($data['paginate']['perPage'], ['*'], 'page', $data['paginate']['page'])
            ->withPath('');
    }

    /**
     * @param array<mixed> &$data
     * @return ProductFilterService
     */
    private function fillDataFilterable(array &$data): ProductFilterService
    {
        $data['filterable'] = cache()->rememberForever('filterable_by_category_id:' . $data['category']->id, function () use ($data) {

            $whereHasProducts = fn(Builder $b) => $b->where('category_id', $data['category']->id);

            $filterable['salers'] = User::query()
                ->whereHas('products', $whereHasProducts)
                ->toBase()
                ->select('id', 'name')
                ->get();

            $filterable['tags'] = Tag::query()
                ->whereHas('products', $whereHasProducts)
                ->toBase()
                ->select('id', 'title')
                ->get();

            $filterable['optionValues'] = OptionValue::query()
                ->whereHas('products', $whereHasProducts)
                ->getAndGroupWithParentTitle();

            $filterable['propertyValues'] = PropertyValue::query()
                ->whereHas('products', $whereHasProducts)
                ->getAndGroupWithParentTitle();

            $filterable['prices'] = (array)$data['category']->productTypes()->selectRaw('MIN(price) AS min, MAX(price) AS max')->toBase()->first();

            return $filterable;
        });
        return $this;
    }

    /**
     * @param array<mixed> &$data
     * @return ProductFilterService
     */
    private function fillDataPaginate(array &$data): ProductFilterService
    {
        $default = [
            'orderBy' => 'rating',
            'perPage' => 8,
            'page' => 1,
        ];
        $data['paginate'] = array_merge($default, $data['paginate'] ?? []);
        return $this;
    }
}
