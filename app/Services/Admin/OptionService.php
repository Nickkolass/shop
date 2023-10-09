<?php

namespace App\Services\Admin;

use App\Dto\Admin\OptionDto;
use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Support\Facades\DB;

class OptionService
{
    public function store(OptionDto $dto): void
    {
        DB::beginTransaction();
        $option_id = Option::query()->firstOrCreate(['title' => $dto->title])->id;
        foreach ($dto->optionValues as $optionValue) {
            $data[] = ['option_id' => $option_id, 'value' => $optionValue];
        }
        if (!empty($data)) OptionValue::query()->insert($data);
        DB::commit();
    }

    public function update(Option $option, OptionDto $dto): void
    {
        $oldValues = $option->optionValues()->pluck('value')->all();
        $newValues = $dto->optionValues;
        $deleteValue = array_diff($oldValues, $newValues);
        $createValue = array_diff($newValues, $oldValues);
        foreach ($createValue as &$optionValue) {
            $optionValue = ['option_id' => $option->id, 'value' => $optionValue];
        }

        DB::beginTransaction();
        $option->optionValues()
            ->where('option_id', $option->id)
            ->whereIn('value', $deleteValue)
            ->delete();
        OptionValue::query()->insert($createValue);
        $option->update(['title' => $dto->title]);
        DB::commit();
    }
}
