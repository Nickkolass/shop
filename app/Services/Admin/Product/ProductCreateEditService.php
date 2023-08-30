<?php

namespace App\Services\Admin\Product;

use App\Models\Category;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Property;
use App\Models\Tag;
use App\Services\Methods\Maper;
use Illuminate\Support\Collection;

class ProductCreateEditService
{

    public function index(): array
    {
        $data['categories'] = collect(cache()->get('categories'))->pluck('title_rus', 'id');
        return $data;
    }

    public function relations(int $category_id): array
    {
        $data['tags'] = Tag::pluck('title', 'id');
        $data['properties'] = Property::query()
            ->whereHas('categories', fn($q) => $q->where('category_id', $category_id))
            ->with('propertyValues:id,property_id,value')
            ->select('id', 'title')
            ->get();
        $data['optionValues'] = Option::query()
            ->with('optionValues:id,option_id,value')
            ->select('id', 'title')
            ->get();
        $data['optionValues'] = Maper::OptionOrPropertyValues($data['optionValues']);
        return $data;
    }

    public function types(array $optionValues): Collection
    {
        $optionValues = OptionValue::query()
            ->with('option:id,title')
            ->select('id', 'option_id', 'value')
            ->find($optionValues);
        return Maper::toGroups($optionValues);
    }
}
