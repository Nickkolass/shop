<?php

namespace App\Services\Admin;

use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Support\Facades\DB;

class OptionService
{
    public function store(array $data): ?string
    {
        DB::beginTransaction();
        try {

            $option_id = Option::firstOrCreate(['title' => $data['title']])->id;
            foreach ($data['optionValues'] as &$optionValue) $optionValue['option_id'] = $option_id;
            OptionValue::insert($data['optionValues']);

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    public function update(Option $option, array $data): ?string
    {
        $oldValues = $option->optionValues()->pluck('value')->all();
        $newValues = array_column($data['optionValues'], 'value');
        $delete = array_diff($oldValues, $newValues);
        $create = array_diff($newValues, $oldValues);
        foreach ($create as &$optionValue) $optionValue = ['option_id' => $option->id, 'value' => $optionValue];

        DB::beginTransaction();
        try {

            $option->optionValues()
                ->where('option_id', $option->id)
                ->whereIn('value', $delete)
                ->delete();
            OptionValue::insert($create);
            $option->update(['title' => $data['title']]);

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
