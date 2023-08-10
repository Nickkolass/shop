<?php

namespace App\Services\Admin\Product\Relations;

use App\Models\PropertyValue;

class PropertyValueService
{

    public function forRelationsProduct(array &$propertyValues): void
    {
        $query = PropertyValue::query();
        foreach ($propertyValues as $property_id => $value) {
            $propertyValues[$property_id] = ['property_id' => $property_id, 'value' => $value];
            $query->orWhere(function ($b) use ($propertyValues, $property_id) {
                $b->where($propertyValues[$property_id]);
            });
        }
        PropertyValue::upsert($propertyValues, ['property_id', 'value']);
        $propertyValues = $query->pluck('id')->all();
    }
}
