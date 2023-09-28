<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductTypeFilter extends AbstractFilter
{
    const PRICES = 'prices';
    const OPTIONVALUES = 'optionValues';

    /** @return array<string, array<int, mixed>> */
    public function getCallbacks(): array
    {
        return [
            self::PRICES => [$this, 'prices'],
            self::OPTIONVALUES => [$this, 'optionValues'],
        ];
    }

    /**
     * @param Builder $builder
     * @param array<string, int> $value
     * @return void
     */
    public function prices(Builder $builder, array $value): void
    {
        $builder->whereBetween('price', [$value['min'] ?? 0, $value['max'] ?? 1000000]);
    }

    /**
     * @param Builder $builder
     * @param array<int, array<int, int>> $value
     * @return void
     */
    public function optionValues(Builder $builder, array $value): void
    {
        foreach ($value as $option_id => $optionValue_ids) {
            $builder->whereHas('optionValues', function ($b) use ($optionValue_ids) {
                $b->whereIn('optionValue_id', $optionValue_ids);
            });
        }
    }
}
