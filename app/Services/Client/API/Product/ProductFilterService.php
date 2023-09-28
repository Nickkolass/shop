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
use App\Services\Methods\Maper;
use Illuminate\Database\Eloquent\Builder;

class ProductFilterService
{

    /**
     * @param array<string, mixed> $data
     * @param Category $category
     * @return array<string, mixed>
     */
    public function getProductFilterAggregateDataCache(array $data, Category $category): array
    {
        if (!empty($data)) $result = $this->getProductFilterAggregateData($data, $category);
        else $result = cache()->rememberForever('first_page_product_aggregate_data_without_filter_by_category_id:' . $category->id,
            fn() => $this->getProductFilterAggregateData($data, $category));
        return $result;
    }

    /**
     * @param array<string, mixed> $data
     * @param Category $category
     * @return array<string, mixed>
     */
    private function getProductFilterAggregateData(array $data, Category $category): array
    {
        $data['filter'] = $data['filter'] ?? [];
        $data['category'] = $category;
        $this->fillDataPaginate($data)->fillDataFilterable($data)->fillDataFilteredProductTypes($data);
        return $data;
    }

    /**
     * @param array<string, array|mixed> &$data
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

    /**
     * @param array<string, mixed> &$data
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
                ->with('option:id,title')
                ->whereHas('products', $whereHasProducts)
                ->select('id', 'option_id', 'value')
                ->get();

            $filterable['propertyValues'] = PropertyValue::query()
                ->with('property:id,title')
                ->whereHas('products', $whereHasProducts)
                ->select('id', 'property_id', 'value')
                ->get();

            $filterable['optionValues'] = Maper::toGroups($filterable['optionValues']);
            $filterable['propertyValues'] = Maper::toGroups($filterable['propertyValues']);

            $prices = $data['category']->productTypes()->selectRaw('MAX(price) AS max, MIN(price) AS min')->first();
            $filterable['prices'] = ['min' => $prices->min, 'max' => $prices->max];

            return $filterable;
        });
        return $this;
    }

    /**
     * @param array<string, mixed> &$data
     * @return ProductFilterService
     */
    private function fillDataFilteredProductTypes(array &$data): ProductFilterService
    {
        $data['productTypes'] = ProductType::query()
            ->filter(new ProductTypeFilter($data['filter']))
            ->sort($data['paginate']['orderBy'])
            ->select('productTypes.id', 'productTypes.product_id', 'is_published', 'preview_image', 'price', 'count')
            ->withExists(['liked' => fn(Builder $q) => $q->where('user_id', auth('api')->id())])
            ->with([
                'productImages:productType_id,file_path',
                'optionValues.option:id,title',
                'product' => function ($q) {
                    $q->select('id', 'title')
                        ->with([
                            'productTypes:id,product_id,is_published,preview_image',
                            'ratingAndComments',
                        ]);
                }
            ])
            ->whereHas('product', function ($q) use ($data) {
                $q->filter(new ProductFilter($data['filter'] + ['category' => $data['category']->id]));
            })
            ->simplePaginate($data['paginate']['perPage'], ['*'], 'page', $data['paginate']['page'])
            ->withPath('');

        Maper::mapAfterGettingProducts($data['productTypes']);
        return $this;
    }
}
