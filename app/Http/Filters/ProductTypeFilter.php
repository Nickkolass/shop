<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductTypeFilter extends AbstractFilter
{
    const PRICES = 'prices';
    const OPTIONVALUES = 'optionValues';

    protected function getCallbacks(): array
    {
        return [
            self::PRICES => [$this, 'prices'],
            self::OPTIONVALUES => [$this, 'optionValues'],
        ];
    }

    public function prices(Builder $builder, $value): void
    {
        $builder->whereBetween('price', [$value['min'] ?? 0, $value['max'] ?? 1000000]);
    }

    public function optionValues(Builder $builder, $value): void
    {
        foreach ($value as $option_id => $optionValue_ids) {
            $builder->whereHas('optionValues', function ($b) use ($optionValue_ids) {
                $b->whereIn('optionValue_id', $optionValue_ids);
            });
        }
    }
}
