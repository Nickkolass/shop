<?php

namespace App\Services\Admin\Product\Relations;

use App\Models\PropertyValue;

class PropertyValueService
{

    /**
     * @param array<int, string> &$propertyValues
     * @return void
     */
    public function upsertPropertyValues(array &$propertyValues): void
    {
        $query = PropertyValue::query();
        foreach ($propertyValues as $property_id => &$value) {
            $value = ['property_id' => $property_id, 'value' => $value];
            $query->orWhere(fn($b) => $b->where($value));
        }
        PropertyValue::query()->upsert($propertyValues, ['property_id', 'value']);
        $propertyValues = $query->pluck('id')->all();
    }
}
