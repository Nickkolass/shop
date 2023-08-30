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

    public function getProductFilterAggregateDataCache(array $data, Category $category): array
    {
        if (!empty($data)) $result = $this->getProductFilterAggregateData($data, $category);
        else $result = cache()->rememberForever('first_page_product_aggregate_data_without_filter_by_category_id:' . $category->id,
            fn() => $this->getProductFilterAggregateData($data, $category));
        return $result;
    }

    private function getProductFilterAggregateData(array $data, Category $category): array
    {
        $data['filter'] = $data['filter'] ?? [];
        $data['category'] = $category;
        $this->fillDataPaginate($data)->fillDataFilterable($data)->fillDataFilteredProductTypes($data);
        return $data;
    }

    private function fillDataPaginate(array &$data): ProductFilterService
    {
        $data['paginate']['orderBy'] = $data['paginate']['orderBy'] ?? 'rating';
        $data['paginate']['perPage'] = $data['paginate']['perPage'] ?? 8;
        $data['paginate']['page'] = $data['paginate']['page'] ?? 1;
        return $this;
    }

    private function fillDataFilterable(array &$data): ProductFilterService
    {
        $data['filterable'] = cache()->rememberForever('filterable_by_category_id:' . $data['category']->id, function () use ($data) {

            $whereHasProducts = fn(Builder $b) => $b->where('category_id', $data['category']->id);

            $filterable['salers'] = User::query()
                ->whereHas('products', $whereHasProducts)
                ->select('id', 'name')
                ->toBase()
                ->get();

            $filterable['tags'] = Tag::query()
                ->whereHas('products', $whereHasProducts)
                ->select('id', 'title')
                ->toBase()
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

    private function fillDataFilteredProductTypes(array &$data): ProductFilterService
    {
        $data['productTypes'] = ProductType::query()
            ->select('productTypes.id', 'productTypes.product_id', 'is_published', 'preview_image', 'price', 'count')
            ->withExists(['liked' => fn($q) => $q->where('user_id', auth('api')->id())])
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
            ->filter(new ProductTypeFilter($data['filter']))
            ->sort($data['paginate']['orderBy'])
            ->simplePaginate($data['paginate']['perPage'], ['*'], 'page', $data['paginate']['page'])
            ->withPath('');

        Maper::mapAfterGettingProducts($data['productTypes']);
        return $this;
    }
}
