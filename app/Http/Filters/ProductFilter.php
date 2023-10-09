<?php

namespace App\Http\Filters;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ProductFilter extends AbstractFilter
{

    const SEARCH = 'search';
    const CATEGORY = 'category';
    const SALERS = 'salers';
    const TAGS = 'tags';
    const PROPERTYVALUES = 'propertyValues';

    /** @return array<mixed> */
    protected function getCallbacks(): array
    {
        return [
            self::SEARCH => [$this, 'search'],
            self::CATEGORY => [$this, 'category'],
            self::SALERS => [$this, 'salers'],
            self::TAGS => [$this, 'tags'],
            self::PROPERTYVALUES => [$this, 'propertyValues'],
        ];
    }

    /**
     * @param Builder $builder
     * @param string $value
     * @return void
     */
    public function search(Builder $builder, string $value): void
    {
        $builder->whereIn('id', Product::search($value)->keys()->all());
    }

    /**
     * @param Builder $builder
     * @param string $value
     * @return void
     */
    public function category(Builder $builder, string $value): void
    {
        $builder->where('category_id', $value);
    }

    /**
     * @param Builder $builder
     * @param array<int> $value
     * @return void
     */
    public function salers(Builder $builder, array $value): void
    {
        $builder->whereIn('saler_id', $value);
    }

    /**
     * @param Builder $builder
     * @param array<int> $value
     * @return void
     */
    public function tags(Builder $builder, array $value): void
    {
        $builder->whereHas('tags', function (Builder $b) use ($value) {
            $b->whereIn('tag_id', $value);
        });
    }

    /**
     * @param Builder $builder
     * @param array<array<int>> $value
     * @return void
     */
    public function propertyValues(Builder $builder, array $value): void
    {
        $builder->whereHas('propertyValues', function (Builder $b) use ($value) {
            $b->selectRaw('COUNT(DISTINCT(`property_id`)) as `counter`')
                ->whereIn('propertyValue_id', Arr::flatten($value))
                ->having('counter', count($value));
        });
    }
}
