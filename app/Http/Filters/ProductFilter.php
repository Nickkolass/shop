<?php


namespace App\Http\Filters;


use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends AbstractFilter
{

    const SEARCH = 'search';
    const CATEGORY = 'category';
    const TAGS = 'tags';
    const SALERS = 'salers';
    const PRICES = 'prices';
    const OPTIONVALUES = 'optionValues';
    const PROPERTYVALUES = 'propertyValues';

    protected function getCallbacks(): array
    {
        return ([
            self::SEARCH => [$this, 'search'],
            self::CATEGORY => [$this, 'category'],
            self::TAGS => [$this, 'tags'],
            self::SALERS => [$this, 'salers'],
            self::PRICES => [$this, 'prices'],
            self::OPTIONVALUES => [$this, 'optionValues'],
            self::PROPERTYVALUES => [$this, 'propertyValues'],
        ]);
    }

    public function search(Builder $builder, $value)
    {
        $builder->whereIn('productTypes.product_id', $value);
    }

    public function category(Builder $builder, $value)
    {
        $builder->whereHas('product', function ($b) use ($value) {
            $b->where('category_id', $value);
        });
    }

    public function tags(Builder $builder, $value)
    {
        $builder->whereHas('product.tags', function ($b) use ($value) {
            $b->whereIn('tag_id', $value);
        });
    }

    public function salers(Builder $builder, $value)
    {
        $builder->whereHas('product', function ($b) use ($value) {
            $b->whereIn('saler_id', $value);
        });
    }

    public function prices(Builder $builder, $value)
    {
        $builder->whereBetween('price', [$value['min'] ?? 0, $value['max'] ?? 1000000]);
    }

    public function optionValues(Builder $builder, $value)
    {
        foreach ($value as $option_id => $optionValue_ids) {
            $builder->whereHas('optionValues', function ($b) use ($optionValue_ids) {
                $b->whereIn('optionValue_id', $optionValue_ids);
            });
        }
    }

    public function propertyValues(Builder $builder, $value)
    {
        foreach ($value as $property_id => $propertyValue_ids) {
            $builder->whereHas('product.propertyValues', function ($q) use ($propertyValue_ids) {
                $q->whereIn('property_value_id', $propertyValue_ids);
            });
        }
    }
}
