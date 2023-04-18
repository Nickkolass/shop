<?php


namespace App\Http\Filters;


use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends AbstractFilter
{

    const CATEGORY = 'category';
    const TAGS = 'tags';
    const SALERS = 'salers';
    const PRICES = 'prices';
    const OPTIONVALUES = 'optionValues';
    const PROPERTYVALUES = 'propertyValues';

    protected function getCallbacks(): array
    {
        return ([
            self::CATEGORY => [$this, 'category'],
            self::TAGS => [$this, 'tags'],
            self::SALERS => [$this, 'salers'],
            self::PRICES => [$this, 'prices'],
            self::OPTIONVALUES => [$this, 'optionValues'],
            self::PROPERTYVALUES => [$this, 'propertyValues'],
        ]);
    }

    public function category(Builder $builder, $value)
    {
        $builder->where('category_id', $value);
    }

    public function tags(Builder $builder, $value)
    {
        $builder->whereHas('tags', function ($b) use ($value) {
            $b->whereIn('tag_id', $value);
        });
    }

    public function salers(Builder $builder, $value)
    {
        $builder->whereIn('saler_id', $value);
    }

    public function prices (Builder $builder, $value)
    {
        $builder->whereBetween('price', $value);
    }

    public function optionValues(Builder $builder, $value)
    {
        foreach ($value as $option_id => $optionValue_ids) {
            $builder->whereHas('optionValues', function ($b) use ($option_id, $optionValue_ids) {
                $b->where('option_id', $option_id)->whereIn('optionValue_id', $optionValue_ids);
            });
        }
    }

    public function propertyValues(Builder $builder, $value)
    {
        foreach ($value as $property_id => $propertyValue_ids) {
            $builder->whereHas('propertyValues', function ($b) use ($property_id, $propertyValue_ids) {
                $b->where('property_id', $property_id)->whereIn('property_value_id', $propertyValue_ids);
            });
        }
    }
}
