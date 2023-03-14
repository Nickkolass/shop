<?php


namespace App\Http\Filters;


use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends AbstractFilter
{

    const CATEGORY = 'category';
    const COLORS = 'colors';
    const PRICES = 'prices';
    const TAGS = 'tags';
    const SALERS = 'salers';


    protected function getCallbacks(): array
    {
        return ([
            self::CATEGORY => [$this, 'category'],
            self::COLORS => [$this, 'colors'],
            self::PRICES => [$this, 'prices'],
            self::TAGS => [$this, 'tags'],
            self::SALERS => [$this, 'salers'],

        ]);
    }

    public function category(Builder $builder, $value)
    {
        $builder->where('category_id', $value);
    }

    public function colors(Builder $builder, $value)
    {
        $builder->whereIn('color_id', $value);
    }

    public function prices(Builder $builder, $value)
    {
        $builder->whereBetween('price', $value);
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
}
