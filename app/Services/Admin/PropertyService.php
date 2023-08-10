<?php

namespace App\Services\Admin;

use App\Models\Property;
use App\Models\PropertyValue;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    public function store(array $data): ?string
    {
        DB::beginTransaction();
        try {

            $property = Property::firstOrCreate(['title' => $data['title']]);
            foreach ($data['propertyValues'] as &$propertyValue) $propertyValue['property_id'] = $property->id;
            PropertyValue::insert($data['propertyValues']);
            $property->categories()->attach($data['categories']);

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    public function update(Property $property, array $data): ?string
    {
        $oldValues = $property->propertyValues()->pluck('value')->all();
        $newValues = array_column($data['propertyValues'], 'value');
        $delete = array_diff($oldValues, $newValues);
        $create = array_diff($newValues, $oldValues);
        foreach ($create as &$propertyValue) $propertyValue = ['property_id' => $property->id, 'value' => $propertyValue];

        DB::beginTransaction();
        try {

            $property->propertyValues()
                ->where('property_id', $property->id)
                ->whereIn('value', $delete)
                ->delete();
            PropertyValue::insert($create);
            $property->update(['title' => $data['title']]);
            $property->categories()->sync($data['categories']);

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
