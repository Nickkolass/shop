<?php

namespace App\Components;

use App\Models\Product;

class Method
{

    public static function optionsAndProperties(Product &$product)
    {
        Method::valuesToKeys($product, 'propertyValues');
        Method::valuesToGroups($product, 'optionValues');
    }


    public static function valuesToKeys(Product &$product, $relationship, ?bool $value_id = false)
    {
        $product->setRelation($relationship, $product->$relationship->mapWithKeys(function ($value) use ($value_id) {
            $key = !empty($value_id) ? $value->option_id ?? $value->property_id : $value->option->title ?? $value->property->title;
            return [$key => $value->value];
        }));
    }


    public static function valuesToGroups(Product &$product, $relationship)
    {
        $product->setRelation($relationship, Method::toGroups($product->$relationship));
    }


    public static function toGroups($relationship)
    {
        return $relationship->mapToGroups(function ($value) {
            return [$value->option->title ?? $value->property->title => $value->unsetRelations()];
        });
    }


    public static function inCart($products, $cart, ?bool $show = false)
    {
        !$show ?: $products = collect([0 => $products]);

        $product_ids_in_cart = array_combine(array_keys($cart), array_column($cart, 'product_id'));

        $products->map(function ($product) use ($product_ids_in_cart, $cart) {
            $key = array_search($product->id, $product_ids_in_cart);
            $key === false ?: $product->inCart = $cart[$key];
        });
        return $products;
    }
}
