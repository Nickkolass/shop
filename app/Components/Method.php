<?php

namespace App\Components;

use App\Models\Product;
use App\Models\ProductType;

class Method
{

    public static function optionsAndProperties(Product &$product)
    {
        Method::valuesToKeys($product, 'propertyValues');
        Method::valuesToGroups($product, 'optionValues');
    }


    public static function valuesToKeys(&$product, $relation, ?bool $value_id = false)
    {
        $product->setRelation($relation, Method::toKeys($product->$relation, $value_id));
    }

    public static function toKeys($relation, ?bool $value_id = false)
    {
        return $relation->mapWithKeys(function ($value) use ($value_id) {
                $key = !empty($value_id) ? $value->option_id ?? $value->property_id : $value->option->title ?? $value->property->title;
                return [$key => $value->value];
            }
        );
    }

    public static function valuesToGroups(&$product, $relation)
    {
        $product->setRelation($relation, Method::toGroups($product->$relation));
    }


    public static function toGroups($relation)
    {
        return $relation->mapToGroups(function ($value) {
            return [$value->option->title ?? $value->property->title => $value->unsetRelations()];
        });
    }

    public static function OVPs($OVPs)
    {
        return $OVPs->mapWithKeys(function ($OVP) {
            $value = $OVP->optionValues ?? $OVP->propertyValues;
            return [$OVP->title => $value->pluck('value', 'id')];
        });
    }

    public static function mapAfterGettingProducts(&$productTypes)
    {
        $productTypes->map(function ($productType) {
            Method::valuesToGroups($productType, 'optionValues');
            Method::countingRatingAndComments($productType);
        });
    }

    public static function countingRatingAndComments(ProductType &$productType)
    {
        $productType->product->rating = round(($productType->product->ratingAndComments->avg('rating') ?? 0)*2)/2;
        $productType->product->countRating = $productType->product->ratingAndComments->count();
        $productType->product->countComments = $productType->product->ratingAndComments->pluck('message')->filter()->count();
    }



}
