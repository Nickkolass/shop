<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\PropertyValue;


class RelationService
{
    public function attach(Product $product, $attach)
    {
        empty($attach['propertyValues']) ?: $attach['propertyValues'] = $this->storePropertyValues($attach['propertyValues']);

        foreach ($attach as $relationship => $keys) {
            $detached[$relationship] = $product->$relationship()->sync($keys)['detached'];
        }

        empty($detached['propertyValues']) ?: PropertyValue::whereIn('id', $detached['propertyValues'])->doesntHave('products')->delete();

    }

    private function storePropertyValues($propertyValues)
    {
        foreach (array_filter($propertyValues) as $property_id => $value) {
            $pV_ids[] = PropertyValue::firstOrCreate(
                [
                    'property_id' => $property_id,
                    'value' => $value,
                ],[
                    'property_id' => $property_id,
                    'value' => $value,
                ]
            )->id;
        }
        return $pV_ids;
    }


    
    public function getAttach(&$data)
    {
        foreach ($data as $column => $keys) {
            if (is_array($keys)) {
                $attach[$column] = $data[$column];
                unset($data[$column]);
            }
        }
        return $attach ?? [];
    }
}
