<?php

namespace App\Services\API\Back;

use App\Http\Filters\ProductFilter;
use App\Components\Method;
use App\Models\Category;
use App\Models\Product;
use App\Models\OptionValue;
use App\Models\ProductType;
use App\Models\ProductTypeUserLike;
use App\Models\PropertyValue;
use App\Models\Tag;
use App\Models\User;


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
        if (isset($data['user_id'])) {
            $data['liked_ids'] = ProductTypeUserLike::where('user_id', $data['user_id'])
                ->whereHas('productType.category', function ($q) use ($data) {
                    $q->where('category_id', $data['category']->id);
                })->pluck('productType_id')->flip()->all();
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
        $products_ids = $data['category']->products()->pluck('id');

        $data['filterable']['optionValues'] = OptionValue::select('id', 'option_id', 'value')->with('option:id,title')
            ->whereHas('products', function ($b) use ($products_ids) {
                $b->whereIn('product_id', $products_ids);
            })->get();

        $data['filterable']['propertyValues'] = PropertyValue::select('id', 'property_id', 'value')->with('property:id,title')
            ->whereHas('products', function ($b) use ($products_ids) {
                $b->whereIn('product_id', $products_ids);
            })->get();

        $data['filterable']['optionValues'] = Method::toGroups($data['filterable']['optionValues']);
        $data['filterable']['propertyValues'] = Method::toGroups($data['filterable']['propertyValues']);

        $data['filterable']['prices'] = [
            'min' => $data['category']->productTypes()->min('price'),
            'max' => $data['category']->productTypes()->max('price'),
        ];

        $data['filterable']['salers'] = User::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('id', $products_ids);
        })->select('id', 'name')->get();

        $data['filterable']['tags'] = Tag::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('product_id', $products_ids);
        })->select('id', 'title')->get();

        return $this;
    }

    private function getProductTypes(array &$data): BackProductsService
    {
        if (!empty($data['filter']['search'])) $search = ['search' => Product::search($data['filter']['search'])->keys()->all()];
        $queryParams = array_merge(array_filter($data['filter']), ['category' => $data['category']->id], $search ?? []);
        $filter = app()->make(ProductFilter::class, ['queryParams' => $queryParams]);

        $data['productTypes'] = ProductType::select('id', 'product_id', 'is_published', 'preview_image', 'price', 'count')
            ->with(['productImages:productType_id,file_path', 'optionValues.option:id,title', 'product' => function ($q) {
                $q->select('id', 'title')->with('productTypes:id,product_id,is_published,preview_image', 'ratingAndComments');
            }])->filter($filter)->sorted($data['paginate']['orderBy'])->simplePaginate($data['paginate']['perPage'], ['*'], 'page', $data['paginate']['page'])->withPath('');

        Method::mapAfterGettingProducts($data['productTypes']);

        return $this;
    }
}
