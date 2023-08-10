<?php

namespace App\Services\API\Back;

use App\Http\Filters\ProductFilter;
use App\Components\Method;
use App\Models\Category;
use App\Models\Product;
use App\Models\OptionValue;
use App\Models\ProductType;
use App\Models\PropertyValue;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BackProductsService
{

    public function getData(array &$data, Category $category): void
    {
        $data['filter'] = $data['filter'] ?? [];
        $data['category'] = $category;
        $this->getPaginate($data)->getProductTypes($data)->getFilterable($data)->getliked($data);
    }

    private function getliked(array &$data): BackProductsService
    {
        if ($user = auth('api')->user()) {
            $data['liked_ids'] = $user
                ->liked()
                ->whereHas('category', function ($q) use ($data) {
                    $q->where('category_id', $data['category']->id);
                })
                ->pluck('productType_id')
                ->flip()
                ->all();
        }
        return $this;
    }

    private function getPaginate(array &$data): BackProductsService
    {
        $data['paginate']['orderBy'] = $data['paginate']['orderBy'] ?? 'rating';
        $data['paginate']['perPage'] = $data['paginate']['perPage'] ?? 8;
        $data['paginate']['page'] = $data['paginate']['page'] ?? 1;
        return $this;
    }

    private function getFilterable(array &$data): BackProductsService
    {
        $whereHasProducts = fn(Builder $b) => $b->where('category_id', $data['category']->id);

        $data['filterable']['salers'] = User::query()
            ->whereHas('products', $whereHasProducts)
            ->select('id', 'name')
            ->get();

        $data['filterable']['tags'] = Tag::query()
            ->whereHas('products', $whereHasProducts)
            ->select('id', 'title')
            ->get();

        $data['filterable']['optionValues'] = OptionValue::query()
            ->with('option:id,title')
            ->whereHas('products', $whereHasProducts)
            ->select('id', 'option_id', 'value')
            ->get();

        $data['filterable']['propertyValues'] = PropertyValue::query()
            ->with('property:id,title')
            ->whereHas('products', $whereHasProducts)
            ->select('id', 'property_id', 'value')
            ->get();

        $data['filterable']['optionValues'] = Method::toGroups($data['filterable']['optionValues']);
        $data['filterable']['propertyValues'] = Method::toGroups($data['filterable']['propertyValues']);

        $prices = $data['category']->productTypes()->selectRaw('MAX(price) AS max, MIN(price) AS min')->first();
        $data['filterable']['prices'] = ['min' => $prices->min, 'max' => $prices->max];

        return $this;
    }

    private function getProductTypes(array &$data): BackProductsService
    {
        $data['productTypes'] = ProductType::query()
            ->select('id', 'product_id', 'is_published', 'preview_image', 'price', 'count')
            ->with([
                'productImages:productType_id,file_path',
                'optionValues.option:id,title',
                'product' => function ($q) {
                    $q->select('id', 'title')
                        ->with([
                            'productTypes:id,product_id,is_published,preview_image',
                            'ratingAndComments',
                        ]);
                }])
            ->filter($this->productFilter($data))
            ->sorted($data['paginate']['orderBy'])
            ->simplePaginate($data['paginate']['perPage'], ['*'], 'page', $data['paginate']['page'])
            ->withPath('');

        Method::mapAfterGettingProducts($data['productTypes']);
        return $this;
    }

    private function productFilter(array $data): ProductFilter
    {
        if (!empty($data['filter']['search'])) $search = ['search' => Product::search($data['filter']['search'])->keys()->all()];
        $queryParams = array_merge(array_filter($data['filter']), ['category' => $data['category']->id], $search ?? []);
        return new ProductFilter($queryParams);
    }
}
