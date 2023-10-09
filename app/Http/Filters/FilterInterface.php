<?php

namespace App\Http\Filters;

use Illuminate\Contracts\Database\Query\Builder;

interface FilterInterface
{
    public function apply(Builder $builder): void;
}
