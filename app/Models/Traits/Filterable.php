<?php

namespace App\Models\Traits;

use App\Http\Filters\FilterInterface;
use Illuminate\Contracts\Database\Query\Builder;

trait Filterable
{
    public function scopeFilter(Builder $builder, FilterInterface $filter): Builder
    {
        $filter->apply($builder);
        return $builder;
    }
}
