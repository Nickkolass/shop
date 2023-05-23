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


class BackProductsService
{

    public function getData(&$data, Category $category)
    {
        $data['filter'] = $data['filter'] ?? [];
        $data['paginate'] = $this->getPaginate($data);
        $data['productTypes'] = $this->getProductTypes($data, $category);
        $data['filterable'] = $this->getFilterable($category);
        $data['category'] = $category;
    }


    private function getPaginate($data)
    {
        return [
            'orderBy' => $data['paginate']['orderBy'] ?? 'latest',
            'perPage' => $data['paginate']['perPage'] ??  8,
            'page' => $data['paginate']['page'] ?? 1,
        ];
    }


    private function getFilterable(Category $category)
    {
        $products_ids = $category->products()->pluck('id');

        $data['optionValues'] = OptionValue::select('id', 'option_id', 'value')->with('option:id,title')
            ->whereHas('products', function ($b) use ($products_ids) {
                $b->whereIn('product_id', $products_ids);
            })->get();

        $data['propertyValues'] = PropertyValue::select('id', 'property_id', 'value')->with('property:id,title')
            ->whereHas('products', function ($b) use ($products_ids) {
                $b->whereIn('product_id', $products_ids);
            })->get();

        $data['optionValues'] = Method::toGroups($data['optionValues']);
        $data['propertyValues'] = Method::toGroups($data['propertyValues']);

        $data['prices'] = [
            'min' => $category->productTypes()->min('price'),
            'max' => $category->productTypes()->max('price'),
        ];

        $data['salers'] = User::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('id', $products_ids);
        })->select('id', 'name')->get();

        $data['tags'] = Tag::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('product_id', $products_ids);
        })->select('id', 'title')->get();

        return $data;
    }


    private function getProductTypes($data, Category $category)
    {
        if(!empty($data['filter']['prices'])) $this->sortPrices($data['filter']['prices'], $category);
        return $this->productTypes($data['filter'], $data['paginate'], $category->id);
    }


    private function sortPrices(&$prices, Category $category)
    {
        $prices['min'] = $prices['min'] ?? $category->productTypes()->min('price');
        $prices['max'] = $prices['max'] ?? $category->productTypes()->max('price');
        $prices['min'] > $prices['max'] ? $prices['min'] = $prices['max'] : '';
        asort($prices);
    }


    private function productTypes($queryParams, $paginate, $category_id)
    {
        if(!empty($queryParams['search'])) $queryParams['search'] = Product::search($queryParams['search'])->get('id')->pluck('id');
        $filter = app()->make(ProductFilter::class, ['queryParams' => array_merge(array_filter($queryParams), ['category' => $category_id])]);

        $productTypes = ProductType::filter($filter)->sorted($paginate['orderBy'])->select('id', 'product_id', 'is_published', 'preview_image', 'price', 'count')
            ->with(['productImages:productType_id,file_path', 'optionValues.option:id,title', 'product' => function ($q) {
                $q->select('id', 'title')->with('productTypes:id,product_id,is_published,preview_image');
            }])->simplePaginate($paginate['perPage'], ['*'], 'page', $paginate['page'])->withPath('');
            
        $productTypes->map(function ($product) {
            Method::valuesToGroups($product, 'optionValues');
        });

        return $productTypes;
    }
}
