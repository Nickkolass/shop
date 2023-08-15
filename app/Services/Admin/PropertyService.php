<?php

namespace App\Services\Admin;

use App\Models\Property;
use App\Models\PropertyValue;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    public function store(array $data): void
    {
        DB::beginTransaction();
        $property = Property::firstOrCreate(['title' => $data['title']]);
        foreach ($data['propertyValues'] as &$propertyValue) $propertyValue['property_id'] = $property->id;
        PropertyValue::insert($data['propertyValues']);
        $property->categories()->attach($data['categories']);
        DB::commit();
    }

    public function update(Property $property, array $data): void
    {
        $oldValues = $property->propertyValues()->pluck('value')->all();
        $newValues = array_column($data['propertyValues'], 'value');
        $delete = array_diff($oldValues, $newValues);
        $create = array_diff($newValues, $oldValues);
        foreach ($create as &$propertyValue) $propertyValue = ['property_id' => $property->id, 'value' => $propertyValue];

        DB::beginTransaction();
        $property->propertyValues()
            ->where('property_id', $property->id)
            ->whereIn('value', $delete)
            ->delete();
        PropertyValue::insert($create);
        $property->update(['title' => $data['title']]);
        $property->categories()->sync($data['categories']);
        DB::commit();
    }
}
