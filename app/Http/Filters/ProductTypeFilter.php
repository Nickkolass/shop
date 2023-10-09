<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ProductTypeFilter extends AbstractFilter
{
    const PRICES = 'prices';
    const OPTIONVALUES = 'optionValues';

    /** @return array<mixed> */
    public function getCallbacks(): array
    {
        return [
            self::PRICES => [$this, 'prices'],
            self::OPTIONVALUES => [$this, 'optionValues'],
        ];
    }

    /**
     * @param Builder $builder
     * @param array<int> $value
     * @return void
     */
    public function prices(Builder $builder, array $value): void
    {
        $builder->whereBetween('price', [$value['min'] ?? 0, $value['max'] ?? 1000000]);
    }

    /**
     * @param Builder $builder
     * @param array<array<int>> $value
     * @return void
     */
    public function optionValues(Builder $builder, array $value): void
    {
        $builder->whereHas('optionValues', function (Builder $b) use ($value) {
            $b->selectRaw('COUNT(DISTINCT(`option_id`)) as `counter`')
                ->whereIn('optionValue_id', Arr::flatten($value))
                ->having('counter', count($value));
        });
    }
}
