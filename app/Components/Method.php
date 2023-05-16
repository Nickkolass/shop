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


    public static function valuesToKeys(&$product, $relationship, ?bool $value_id = false)
    {
        $product->setRelation($relationship, Method::toKeys($product->$relationship, $value_id));
    }

    public static function toKeys($relationship, ?bool $value_id = false)
    {
        return $relationship->mapWithKeys(function ($value) use ($value_id) {
                $key = !empty($value_id) ? $value->option_id ?? $value->property_id : $value->option->title ?? $value->property->title;
                return [$key => $value->value];
            }
        );
    }

    public static function valuesToGroups(&$product, $relationship)
    {
        $product->setRelation($relationship, Method::toGroups($product->$relationship));
    }


    public static function toGroups($relationship)
    {
        return $relationship->mapToGroups(function ($value) {
            return [$value->option->title ?? $value->property->title => $value->unsetRelations()];
        });
    }

    public static function OVPs($OVPs)
    {
        return $OVPs->mapWithKeys(function ($OVP) {
            $value = $OVP->propertyValues ?? $OVP->optionValues;
            return [$OVP->title => $value->pluck('value', 'id')];
        });
    }
}
