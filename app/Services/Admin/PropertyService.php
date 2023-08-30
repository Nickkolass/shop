<?php

namespace App\Services\Admin;

use App\Dto\Admin\PropertyDto;
use App\Models\Property;
use App\Models\PropertyValue;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    public function store(PropertyDto $dto): void
    {
        DB::beginTransaction();
        $property = Property::firstOrCreate(['title' => $dto->title]);
        foreach ($dto->propertyValues as $propertyValue) $data[] = ['property_id' => $property->id, 'value' => $propertyValue];
        PropertyValue::insert($data);
        $property->categories()->attach($dto->category_ids);
        DB::commit();
    }

    public function update(Property $property, PropertyDto $dto): void
    {
        $oldValues = $property->propertyValues()->pluck('value')->all();
        $newValues = $dto->propertyValues;
        $deleteValues = array_diff($oldValues, $newValues);
        $createValues = array_diff($newValues, $oldValues);
        foreach ($createValues as &$propertyValue) $propertyValue = ['property_id' => $property->id, 'value' => $propertyValue];

        DB::beginTransaction();
        $property->propertyValues()
            ->where('property_id', $property->id)
            ->whereIn('value', $deleteValues)
            ->delete();
        PropertyValue::insert($createValues);
        $property->update(['title' => $dto->title]);
        $property->categories()->sync($dto->category_ids);
        DB::commit();
    }
}
