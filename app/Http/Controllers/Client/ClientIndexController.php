<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Filters\ProductFilter;
use App\Http\Requests\API\Product\FilterRequest;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Tag;

class ClientIndexController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('client.index', compact('categories'));
    }

    public function about()
    {
        $categories = Category::all();
        return view('client.about', compact('categories'));
    }

    public function cart()
    {
        $categories = Category::all();
        return view('client.cart', compact('categories'));
    }

    public function show($category, Product $product)
    {
        $categories = Category::all();
        $category = Category::where('title', $category)->first();
        $colors = $product->colors()->get()->toArray();
        $productImages = $product->productImages()->get()->toArray();
        $groupProducts = $product->group()->first()->products()->get();
        

        return view('client.product', compact('category', 'categories', 'product', 'colors', 'groupProducts', 'productImages'));
    }



    public function products(FilterRequest $request)
    {
        $data = $request->validated();

        if (empty($data['category'])) {
            $data['category'] = Category::where('title', str_word_count($_SERVER['REQUEST_URI'], 1)['2'])->pluck('id')->toArray()['0'];
        }
        if (isset($data['prices'])) {
            if (is_null($data['prices']['maxPrice']) && is_null($data['prices']['minPrice'])) {
                unset($data['prices']['maxPrice'], $data['prices']['minPrice'], $data['prices']);
            } elseif (is_null($data['prices']['maxPrice'])) {
                $data['prices']['maxPrice'] = '1000000';
            } elseif (is_null($data['prices']['minPrice'])) {
                $data['prices']['minPrice'] = '0';
            }
        }

        $filter = app()->make(ProductFilter::class, ['queryParams' => array_filter($data)]);
        $products = Product::filter($filter)->paginate(6)->appends($data);;

        $category = Category::find($data['category']);
        $categories = Category::all();
        $prods = $category->products()->get();

        foreach ($prods as $prod) {
            $tag_ids[] = $prod->tags()->pluck('tag_id')->toArray();
            $color_ids[] = $prod->colors()->pluck('color_id')->toArray();

            $saler_id = $prod->salers()->pluck('name', 'id')->toArray();
            $salers[] = [
                'id' => array_keys($saler_id)['0'],
                'name' => array_values($saler_id)['0'],
            ];
        }

        foreach ($products as $product) {
            $productImages[] = [
                $product->id => $product->productImages()->get()->toArray(),
            ];
        }

        $productImages = (array_replace(...$productImages));


        $salers = array_map('unserialize', array_unique(array_map('serialize', $salers)));
        $tag_ids = array_unique(array_merge(...$tag_ids));
        $color_ids = array_unique(array_merge(...$color_ids));
        foreach ($tag_ids as $tag_id) {
            $tags[] = Tag::find($tag_id)->toArray();
        }
        foreach ($color_ids as $color_id) {
            $colors[] = Color::find($color_id)->toArray();
        }
        $prices = [
            'maxPrice' => $category->products()->max('price'),
            'minPrice' => $category->products()->min('price'),
        ];


        //  return ProductResource::collection($products);

        return view('client.products', compact('products', 'tags', 'colors', 'salers', 'prices', 'category', 'categories', 'data', 'productImages'));
    }
}
