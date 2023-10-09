<?php

namespace App\Services\Admin\Product\Relations;

use App\Models\PropertyValue;
use Illuminate\Contracts\Database\Eloquent\Builder;

class PropertyValueService
{

    /**
     * @param array<string> &$propertyValues
     * @return void
     */
    public function upsertPropertyValues(array &$propertyValues): void
    {
        $query = PropertyValue::query();
        foreach ($propertyValues as $property_id => &$value) {
            $value = ['property_id' => $property_id, 'value' => $value];
            $query->orWhere(fn(Builder $b) => $b->where($value));
        }
        $query->upsert($propertyValues, ['property_id', 'value']);
        $propertyValues = $query->pluck('id')->all();
    }
}
