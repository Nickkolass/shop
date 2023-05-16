<?php

namespace App\Services\Product;

use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Property;
use App\Models\PropertyValue;


class RelationService
{

    public function getSync(&$product)
    {
        foreach ($product as $column => $keys) {
            if (is_array($keys)) {
                $sync[$column] = $product[$column];
                unset($product[$column]);
            }
        }
        return $sync ?? [];
    }


    public function sync(Product $product, $sync)
    {
        empty($sync['propertyValues']) ?: $sync['propertyValues'] = $this->storePropertyValues($sync['propertyValues']);

        foreach ($sync as $relationship => $keys) {
            $detached[$relationship] = $product->$relationship()->sync($keys)['detached'];
        }

        empty($detached['propertyValues']) ?: PropertyValue::whereIn('id', $detached['propertyValues'])->doesntHave('products')->delete();

        empty($detached['optionValues']) ?: $product->productTypes()->whereHas('optionValues', function ($q) use ($detached) {
            $q->whereIn('optionValues.id', $detached['optionValues']);
        })->update(['is_published' => 0]);
    }


    private function storePropertyValues($propertyValues)
    {
        $proderty = Property::whereIn('title', array_keys($propertyValues))->pluck('id', 'title');

        foreach ($propertyValues as $property_title => $value) {
            $pV_ids[] = PropertyValue::firstOrCreate(
                [
                    'property_id' => $proderty[$property_title],
                    'value' => $value,
                ],
                [
                    'property_id' => $proderty[$property_title],
                    'value' => $value,
                ]
            )->id;
        }
        return $pV_ids;
    }

    public function updateProductOVs(ProductType $productType, $optionValues_ids)
    {
        $optionValue_ids = OptionValue::whereHas('productTypes', function ($pT) use ($productType) {
            $pT->where('product_id', $productType->product_id);
        })->pluck('optionValues.id');

        $productType->product->optionValues()->sync($optionValue_ids);
    }
}
