<?php

namespace App\Services\Product;

use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\PropertyValue;


class RelationService
{

    public function getRelations(&$product): array
    {
        foreach ($product as $relation => $keys) {
            if (is_array($keys)) {
                $res[$relation] = $keys;
                unset($product[$relation]);
            }
        }
        return $res ?? [];
    }


    public function relationsType(Product $product, ProductType $productType, $relations, $isNewProduct): array
    {
        if ($isNewProduct) {
            $relations['optionValues'] = array_map(function($optionValue) use ($productType) {
                return ['productType_id' => $productType->id, 'optionValue_id' => $optionValue];
            }, $relations['optionValues']);
        } else {
            $productType->optionValues()->attach($relations['optionValues']);
            $product->optionValues()->sync($relations['optionValues'], false);
        }
        return $relations;
    }


    public function relationsProduct(Product $product, $relations, ?bool $isNewProduct = true): void
    {
        $query = PropertyValue::query();
        foreach ($relations['propertyValues'] as $property_id => $value) {
            $propertyValues[$property_id] = ['property_id' => $property_id, 'value' => $value];
            $query->orWhere(function($b) use ($propertyValues, $property_id) {
                $b->where($propertyValues[$property_id]);
            });
        }
        PropertyValue::upsert($propertyValues, ['property_id', 'value']);
        $relations['propertyValues'] = $query->pluck('id')->all();

        if ($isNewProduct) foreach ($relations as $relation => $keys) $product->$relation()->attach($keys);
        else foreach ($relations as $relation => $keys) $detached[$relation] = $product->$relation()->sync($keys)['detached'];

        if (!empty($detached['optionValues'])) $product->productTypes()->whereHas('optionValues', function ($q) use ($detached) {
            $q->whereIn('optionValues.id', $detached['optionValues']);
        })->update(['is_published' => 0]);
    }


    public function updateProductOVs(ProductType $productType): void
    {
        $optionValue_ids = OptionValue::whereHas('productTypes', function ($pT) use ($productType) {
            $pT->where('product_id', $productType->product_id);
        })->pluck('optionValues.id');

        $productType->product->optionValues()->sync($optionValue_ids);
    }
}
