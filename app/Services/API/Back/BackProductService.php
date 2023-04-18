<?php

namespace App\Services\API\Back;

use Illuminate\Database\Eloquent\Builder;
use App\Http\Filters\ProductFilter;
use App\Components\Method;
use App\Models\Category;
use App\Models\Product;
use App\Models\OptionValue;
use App\Models\PropertyValue;
use App\Models\Tag;
use App\Models\User;


class BackProductService
{
    
    public function getData(&$data, Category $category)
    {
        $cart = empty($data['cart']) ? [] : array_pop($data);
        $data['filter'] = $data['filter'] ?? [];
        $data['paginate'] = $this->getPaginate($data);
        $data['products'] = $this->getProducts($data, $category->id, $cart);
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
            'min' => $category->products()->min('price'),
            'max' =>  $category->products()->max('price'),
        ];

        $data['salers'] = User::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('id', $products_ids);
        })->select('id', 'name')->get();

        $data['tags'] = Tag::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('product_id', $products_ids);
        })->select('id', 'title')->get();

        return $data;
    }
    

    private function getProducts($data, $category_id, $cart)
    {
        $data['filter']['prices'] ?? false ? $this->sortPrices($data['filter']['prices'], $category_id) : '';
        $products = $this->products($data['filter'], $data['paginate'], $category_id);
        !empty($cart) ?: $products = Method::inCart($products, $cart);
        return $products;
    }


    private function sortPrices(&$prices, $category_id)
    {
        $prices['min'] = $prices['min'] ?? Product::where('category_id', $category_id)->min('price');
        $prices['max'] = $prices['max'] ?? Product::where('category_id', $category_id)->max('price');
        $prices['min'] > $prices['max'] ? $prices['min'] = $prices['max'] : '';
        asort($prices);
    }


    private function products($queryParams, $paginate, $category_id)
    {
        $filter = app()->make(ProductFilter::class, ['queryParams' => array_merge(array_filter($queryParams), ['category' => $category_id])]);
        $products = Product::search($queryParams['search'] ?? null)
            ->query(function (Builder $query) use ($filter, $paginate) {
                $query->filter($filter)->sorted($paginate['orderBy'])
                    ->select('id', 'title', 'count', 'is_published', 'price', 'preview_image')
                    ->with(['productImages:product_id,file_path', 'optionValues.option:id,title']);
            })
            ->simplePaginate($paginate['perPage'], 'page', $paginate['page'])->withPath('');

        $products->map(function ($product) {
            Method::valuesToGroups($product, 'optionValues');
        });

        return $products;
    }
}
