<?php

namespace App\Services\Admin\Product;

use App\Models\OptionValue;
use App\Models\PropertyValue;
use App\Models\Tag;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductCreateEditService
{

    /**
     * @return array<mixed>
     */
    public function index(): array
    {
        $data['categories'] = collect((array)Cache::get('categories'))->pluck('title_rus', 'id');
        return $data;
    }

    /**
     * @param int $category_id
     * @return array<mixed>
     */
    public function relations(int $category_id): array
    {
        $data['tags'] = Tag::query()->pluck('title', 'id');
        $data['optionValues'] = OptionValue::query()->getAndGroupWithParentTitle();
        $data['propertyValues'] = PropertyValue::query()
            ->whereHas('property.categories', fn(Builder $q) => $q->where('category_id', $category_id))
            ->getAndGroupWithParentTitle();
        return $data;
    }

    /**
     * @param array<int> $optionValues
     * @return Collection<int|string, Collection<int|string, mixed>>
     */
    public function types(array $optionValues): Collection
    {
        /** @phpstan-ignore-next-line */
        return OptionValue::query()
            ->whereIn('optionValues.id', $optionValues)
            ->getAndGroupWithParentTitle();
    }
}
