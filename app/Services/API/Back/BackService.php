<?php

namespace App\Services\API\Back;

use App\Components\Method;
use App\Models\Product;


class BackService
{

    public function viewed($data)
    {
        $product_ids = array_slice(value(array_unique(array_reverse($data['products']))), 0, 12);

        $products = Product::select('id', 'title', 'count', 'is_published', 'price', 'preview_image', 'category_id')
            ->with(['productImages:product_id,file_path', 'category:id,title', 'optionValues.option:id,title'])
            ->find($product_ids);

        $products->map(function ($product) {
            Method::valuesToGroups($product, 'optionValues');
        });

        empty($data['cart']) ?: $products = Method::inCart($products, $data['cart']);;

        return $products;
    }

  
    public function product(Product &$product, $cart)
    {
        $product->load([
            'saler:id,name', 'productImages:product_id,file_path', 'optionValues.option:id,title',
            'category:id,title,title_rus', 'propertyValues.property:id,title'
        ]);
        
        empty($cart) ?: $product = Method::inCart($product, $cart, true)->first();
        Method::optionsAndProperties($product);
    }


    public function cart($cart)
    {
        foreach ($cart as $key => &$product) {
            $prod = $product;
            $product = Product::select('id', 'title', 'category_id', 'saler_id', 'preview_image', 'price', 'count')
                ->with(['category:id,title', 'saler:id,name', 'optionValues' => function ($q) use ($prod) {
                    $q->select('optionValues.id', 'option_id', 'value')->whereIn('optionValues.id', $prod['optionValues'])->with('option:id,title');
                }])->find($prod['product_id']);

            $product->amount = $prod['amount'];
            $product->cart_id = $key;
        }
        return $cart;
    }
}
