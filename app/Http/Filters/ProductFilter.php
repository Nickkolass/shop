<?php

namespace App\Http\Filters;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends AbstractFilter
{

    const SEARCH = 'search';
    const CATEGORY = 'category';
    const TAGS = 'tags';
    const SALERS = 'salers';
    const PROPERTYVALUES = 'propertyValues';

    /** @return array<string, array<int, mixed>> */
    protected function getCallbacks(): array
    {
        return [
            self::SEARCH => [$this, 'search'],
            self::CATEGORY => [$this, 'category'],
            self::TAGS => [$this, 'tags'],
            self::SALERS => [$this, 'salers'],
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
     * @param array<int, int> $value
     * @return void
     */
    public function tags(Builder $builder, array $value): void
    {
        $builder->whereHas('tags', function ($b) use ($value) {
            $b->whereIn('tag_id', $value);
        });
    }

    /**
     * @param Builder $builder
     * @param array<int, int> $value
     * @return void
     */
    public function salers(Builder $builder, array $value): void
    {
        $builder->whereIn('saler_id', $value);
    }

    /**
     * @param Builder $builder
     * @param array<int, array<int, int>> $value
     * @return void
     */
    public function propertyValues(Builder $builder, array $value): void
    {
        foreach ($value as $property_id => $propertyValue_ids) {
            $builder->whereHas('propertyValues', function ($q) use ($propertyValue_ids) {
                $q->whereIn('property_value_id', $propertyValue_ids);
            });
        }
    }
}
