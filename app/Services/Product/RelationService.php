<?php

namespace App\Services\Product;

use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\PropertyValue;


class RelationService
{

    public function getRelations(&$product)
    {
        foreach ($product as $relation => $keys) {
            if (is_array($keys)) {
                $relations[$relation] = $product[$relation];
                unset($product[$relation]);
            }
        }
        return $relations ?? [];
    }


    public function relationsType(Product $product, ProductType $productType, $relations, $isNewProduct)
    {
        if ($isNewProduct) {
            $relations['optionValues'] = array_map(function($optionValue) use ($productType) {
                return ['productType_id' => $productType->id, 'optionValue_id' => $optionValue];
            }, $relations['optionValues']);

            return $relations;
        } else {
            $productType->optionValues()->attach($relations['optionValues']);
            $product->optionValues()->sync($relations['optionValues'], false);
        }
    }


    public function relationsProduct(Product $product, $relations, ?bool $isNewProduct = true)
    {
        foreach ($relations['propertyValues'] as $property_id => &$value) {
            $value = PropertyValue::firstOrCreate(['property_id' => $property_id, 'value' => $value])->id;
        }

        if ($isNewProduct) foreach ($relations as $relation => $keys) $product->$relation()->attach($keys);
        else foreach ($relations as $relation => $keys) $detached[$relation] = $product->$relation()->sync($keys)['detached'];

        if (!empty($detached['propertyValues'])) PropertyValue::whereIn('id', $detached['propertyValues'])->doesntHave('products')->delete();
        if (!empty($detached['optionValues'])) $product->productTypes()->whereHas('optionValues', function ($q) use ($detached) {
            $q->whereIn('optionValues.id', $detached['optionValues']);
        })->update(['is_published' => 0]);
    }


    public function updateProductOVs(ProductType $productType)
    {
        $optionValue_ids = OptionValue::whereHas('productTypes', function ($pT) use ($productType) {
            $pT->where('product_id', $productType->product_id);
        })->pluck('optionValues.id');

        $productType->product->optionValues()->sync($optionValue_ids);
    }
}
