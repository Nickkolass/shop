<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Filters\ProductFilter;
use App\Http\Requests\API\Product\FilterRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\ProductShowResource;
use App\Http\Resources\ProductsResource;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;

class BackController extends Controller
{
    public function products(Category $category, FilterRequest $request)
    {
        $data['request'] = $request->validated();
        // return $data;
        if ($data['request']['is_published'] == 1) {
            $products_ids =  $category->products()->where('is_published', 1)->pluck('id');
        } else {
            $products_ids =  $category->products()->pluck('id');
        }
        $data['category'] = $category;
        $data['salers'] = User::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('id', $products_ids);
        })->select('id', 'name')->get();
        $data['colors'] = Color::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('id', $products_ids);
        })->select('id', 'title')->get();
        $data['tags'] = Tag::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('product_id', $products_ids);
        })->select('id', 'title')->get();
        $data['prices'] =  [
            'minPrice' => $category->products()->min('price'),
            'maxPrice' => $category->products()->max('price'),
        ];
        $filter = app()->make(ProductFilter::class, ['queryParams' => array_filter($data['request'])]);
        unset($data['request']['category']);
        $query = Product::with(['tags:id,title', 'color:id,title', 'saler:id,name', 'productImages:product_id,file_path'])->filter($filter);
        $data['request']['is_published'] == 1 ? $query = $query->where('is_published', 1) : '';
        //для сортировки по is_published наверху вместо кавычек $query = $query->orderBy('is_published', 'desc')
        $data['request']['orderBy'] == 'latest' ? $query = $query->latest() : $query = $query->orderBy('price', $data['request']['orderBy']);
        $data['products'] = $query->paginate($data['request']['perPage'], ['*'], 'page', $data['request']['page'])->withPath('');
        return new ProductsResource($data);
    }

    public function product($category, Product $product)
    {
        $data['product'] = $product;
        $data['category'] = $category;
        return new ProductShowResource($data);
    }

    public function cart()
    {
        $product_ids = array_keys($_REQUEST);
        $data = Product::find($product_ids)->sortBy(function ($i, $k) use ($product_ids) {
            return array_search($i->id, $product_ids);
        });
        return CartResource::collection($data);
    }
}
