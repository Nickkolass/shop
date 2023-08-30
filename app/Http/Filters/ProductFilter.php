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

    public function search(Builder $builder, $value): void
    {
        $builder->whereIn('id', Product::search($value)->keys()->all());
    }

    public function category(Builder $builder, $value): void
    {
        $builder->where('category_id', $value);
    }

    public function tags(Builder $builder, $value): void
    {
        $builder->whereHas('tags', function ($b) use ($value) {
            $b->whereIn('tag_id', $value);
        });
    }

    public function salers(Builder $builder, $value): void
    {
        $builder->whereIn('saler_id', $value);
    }

    public function propertyValues(Builder $builder, $value): void
    {
        foreach ($value as $property_id => $propertyValue_ids) {
            $builder->whereHas('propertyValues', function ($q) use ($propertyValue_ids) {
                $q->whereIn('property_value_id', $propertyValue_ids);
            });
        }
    }
}
